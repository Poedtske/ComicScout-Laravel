<?php

namespace App\Scraper;

use App\Models\Serie;
use App\Models\Chapter;
use App\Models\Scanlator;
use App\Exceptions\InvalidScanlatorException;
use Symfony\Component\BrowserKit\HttpBrowser;
use App\Exceptions\SerieAlreadyPresentException;
use App\Exceptions\ChapterAlreadyPresentException;


abstract class Scraper implements IScraper
{
    // protected static $serieCounter=0;//total series scraped

    protected $src;//src name of scraper
    protected $requestMaxBeforeCooldown=10;//max amount of requests before cooldown
    protected $requestCounter;//counts requests before cooldown
    protected $cooldownCounter=0;//counts cooldown
    protected $client;//for scraping
    protected $data=[];//stores all info needed to make chapters and series

    //series selectors
    protected $seriesList;//selector for the serie list
    protected $serieUrl;//selector for the url of the serie page
    protected $serieTitle;//selector for the title of the serie
    protected $serieCover;//selector for the cover of the serie

    //chapter selectors
    protected $chaptersList;//selector for the chapter list
    protected $chapterTitle;//selector for the chapter title
    protected $chapterUrl;//selector for the chapter url

    protected $db;//true: no cout|yes database; false yes cout|no database

    /**
     * check if database is needed (for testing)
     * will create a client
     */
    public function __construct($db) {
        $this->client = new HttpBrowser();
        $this->db=$db;
    }

    /**
     * collects chapters and add them in an array, then reverses the array and start making the chapters and associating it with the serie
     * this way the latest chapter will also have the highest id-number, this makes it easier to display by sorting on id
     */
    protected function createChapters($chapterCrawler,$serie) {
        $chapterList = $chapterCrawler->filter($this->chaptersList);
        $chapterArray=[];

        $chapterList->each(function($node) use($serie,&$chapterArray)  {
            $chapterTitle = $node->filter($this->chapterTitle)->text();
            $chapterUrl = $node->filter($this->chapterUrl)->attr('href');
            $chapter=['title'=>$chapterTitle,
            'url'=>$chapterUrl,];

            $allowed=self::validateChapter($chapterTitle,$serie);
            if($allowed){
                $chapterArray[]=$chapter;
            }
        });
        $chapterArray=array_reverse($chapterArray);

        foreach ($chapterArray as $chap) {
            $chapter= new Chapter();
                $chapter->title=$chap['title'];
                $chapter->url=$chap['url'];
                $chapter->serie()->associate($serie);
                $chapter->save();
        };
    }

    /**
     * is used to update the current series, will take the series with status that are ongoing or unknown, check their page of the ammount of chapters an compare it with the ones in the serie
     */
    public function serieUpdater(){
        $scanlator=Scanlator::where("name",$this->src)->first();
        Serie::where('scanlator_id', $scanlator->id)->get()->each(function ($serie) {
            // Check if the status is either "ongoing" or "Ongoing"
            if (strcasecmp(trim(strtolower($serie->status)), 'ongoing') === 0 || strcasecmp(trim(strtolower($serie->status)), 'unknown') === 0) {
                self::requestCooldown();
                $chapterCrawler = $this->client->request('GET', $serie->url);

                // Compare the count of chapters on the website with the count of chapters stored in the database
                if (count($chapterCrawler->filter($this->chaptersList)) == count($serie->chapters)) {
                    return;
                } else {
                    $this->createChapters($chapterCrawler, $serie);
                }
            }

        });
    }

    /**
     * will add extra info like: status, author, type, artists
     * depends heavily from site to site so will be made in child
     */
    protected abstract function addExtraInfo($chapterCrawler);

    /**
     * to prevent sending to much requests and being blocked from a site we can only send max 20 request/min, it changes to 4 requests/min with reaper scans(localy declared)
     */
    public function requestCooldown()
    {

        //echo $this->requestCounter;
        if($this->requestCounter>=$this->requestMaxBeforeCooldown){
            //echo 'going to sleep';
            $this->cooldownCounter++;
            // echo $this->counter;
            sleep(30);
            $this->requestCounter=0;
        }
        else{
            $this->requestCounter++;
        }
    }


    /**
     * This checks if a chapter is already present in the serie
     */
    public static function checkForDoubleChapter($title,$serie){
        $existingSerie = Chapter::where('title', $title)
                                ->where('serie_id',$serie->id)
                                ->first();
        if($existingSerie){
        throw new ChapterAlreadyPresentException("chapter".$title."is already present in serie ".$serie->title);
        }
    }

    /**
     * is the function called to start the validation of a chapter,
     * calls checkForDoubleChapter and handels error
     */
    public static function validateChapter($title,$serie){
        try {
            self::checkForDoubleChapter($title,$serie);
        } catch (ChapterAlreadyPresentException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * is the function called to start the validation of a serie,
     * calls checkScanlator to check if the scanlator exists
     * calls checkForDoubleSerie to check if to serie to be made is already present by checking the title and scanlator
     * when the information passed the checks it will call addExtraInfo to add the available info
     *
     */
    private function validateSerie($chapterCrawler){
        try {
            $scanlator=self::checkScanlator();
        } catch (InvalidScanlatorException $e) {
            echo "Error: " . $e->getMessage();
            throw $e;
        }
        // $scanlator=Scanlator::where('name',$data['src'])->get();

        try {
            self::checkForDoubleSerie($this->data['title'],$scanlator);
        } catch (SerieAlreadyPresentException $e) {
            return ['allowed'=>false,
            'scanlator'=>$scanlator];
        }

        $serieInfo = $this->addExtraInfo($chapterCrawler);

                        // $serieAuthor = $serieInfo['serieAuthor'];
                        // $serieArtists = $serieInfo['serieArtists'];
                        // $seriePublisher = $serieInfo['serieCompany'];
                        // $serieType = $serieInfo['serieType'];

                        // //$serieDescription=$serieInfo['serieDescription'];
                        // $serieStatus = $serieInfo['serieStatus'];
                        // $serieGenres = $serieInfo["serieGenres"];
                        $this->data=array_merge($this->data,[
                            'author' => $serieInfo['serieAuthor'],
                            'artists' => $serieInfo['serieArtists'],
                            'publisher' => $serieInfo['serieCompany'],
                            'type' => $serieInfo['serieType'],
                            'status' => $serieInfo['serieStatus'],
                        ]);
        return ['allowed'=>true,
                'scanlator'=>$scanlator];


    }

    /**
     * creates serie after calling validateSerie to check if it is allowed to make the serie
     * after making the serie and associating it with the correct scanlator it will call checkBrotherSeries to look for related series
     * after this it will call createChapters to add the chapters
     */
    public function createSerie($chapterCrawler){

        if($this->db==false){
            $info=[];
            $info=$this->addExtraInfo($chapterCrawler);
            echo self::toString($info);


            $chapterList = $chapterCrawler->filter($this->chaptersList);
            echo $chapterList->html();
            $chapterArray=[];

            $chapterList->each(function($node) use(&$chapterArray)  {
                $chapterTitle = $node->filter($this->chapterTitle)->text();
                $chapterUrl = $node->filter($this->chapterUrl)->attr('href');
                $chapter=['title'=>$chapterTitle,
                'url'=>$chapterUrl,];
            });
            $chapterArray=array_reverse($chapterArray);

            foreach ($chapterArray as $chap) {
                echo "\n\nTitle:".$chap['title']."\nUrl:".$chap['url'];
            };
        }
        $result=self::validateSerie($chapterCrawler);
        $scanlator=$result['scanlator'];
        $allowed=$result['allowed'];

        if($allowed){
            $serie=new Serie();

            $serie->status=$this->data['status'];
            $serie->title=$this->data['title'];
            $serie->url=$this->data['url'];
            $serie->cover=$this->data['cover'];
            $serie->author=$this->data['author'];
            $serie->company=$this->data['publisher'];
            $serie->artists=$this->data['artists'];
            $serie->type=$this->data['type'];
            $serie->description=null;
            $serie->scanlator()->associate($scanlator);
            $serie->save();
            $this->checkBrotherSeries($serie);
            // echo "globalSerieCounter: ".++$this->serieCounter."\n";
            //create chapters



            $this->createChapters($chapterCrawler,$serie);
        }
    }

    /**
     * will add related series with the same title and associate them both ways
     */
    protected function checkBrotherSeries($serie)
    {
        $brotherSeries = Serie::whereRaw('LOWER(TRIM(title)) = ?', [strtolower(trim($serie->title))])
                            ->where('scanlator_id', '!=', $serie->scanlator_id)
                            ->get();

        $brotherSeries->each(function ($brother) use ($serie) {
            // Associate $brother with $serie here
            // For example:
            $brother->relatedSeries()->attach($serie->id);
            $serie->relatedSeries()->attach($brother->id);
        });
    }


    /**
     * is used for testing, prints out all fields of a serie
     */
    protected function toString($array){
        $this->data['author']=$array['serieAuthor'];
        $this->data['artists']=$array['serieArtists'];
        $this->data['publisher']=$array['serieCompany'];
        $this->data['type']=$array['serieType'];
        $this->data['status']=$array['serieStatus'];


        return "\n\nAuthor: " . $this->data['author'] . "\n" .
        "Artists: " . $this->data['artists'] . "\n" .
        "Publisher: " . $this->data['publisher'] . "\n" .
        "Type: " . $this->data['type'] . "\n" .
        "Status: " . $this->data['status'] . "\n" .
        "URL: " . $this->data['url'] . "\n" .
        "Title: " . $this->data['title'] . "\n" .
        "Cover: " . $this->data['cover'];
    }

    //checks if the scanlator exists in the databank
    private function checkScanlator(){
        $scanlator=Scanlator::where('name',$this->src)->first();
        if($scanlator===null){
            throw new InvalidScanlatorException("This scanlator:".$this->src." does not exist");
        }
        else{
            return $scanlator;
        }
    }

    //checks if there is a serie with the same name and scanlator
    private function checkForDoubleSerie($title,$scanlator){
        $existingSerie = Serie::where('title', $title)
                            ->where('scanlator_id', $scanlator->id)
                            ->first();
        if($existingSerie){
            throw new SerieAlreadyPresentException("serie".$title."is already present in scanlator".$scanlator);
        }
    }
}
