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
    protected const REQUEST_MAX_BEFORE_COOLDOWN=10;//max amount of requests before cooldown
    protected $requestCounter;//counts requests before cooldown
    protected $cooldownCounter=0;//counts cooldown
    protected $client;//for scraping
    protected $data=[];//stores all info needed to make chapters and series

    //series selectors
    protected $seriesList;
    protected $serieUrl;
    protected $serieTitle;
    protected $serieCover;

    //chapter selectors
    protected $chaptersList;
    protected $chapterTitle;
    protected $chapterUrl;

    protected $db;


    public function __construct($db) {
        $this->client = new HttpBrowser();
        $this->db=$db;
    }

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

    public function serieUpdater(){
        $scanlator=Scanlator::where("name",$this->src)->first();
        Serie::where('scanlator_id',$scanlator->id)->get()->where('status','Ongoing')->each(function($serie){
            self::requestCooldown();
            $chapterCrawler=$this->client->request('GET', $serie->url);
            if(count($chapterCrawler->filter($this->chaptersList))==count($serie->chapters)){
                return;
            }
            else{
                self::createChapters($chapterCrawler,$serie);
            }
        });
    }

    protected abstract function addExtraInfo($chapterCrawler);


    public function requestCooldown()
    {

        //echo $this->requestCounter;
        if($this->requestCounter>=self::REQUEST_MAX_BEFORE_COOLDOWN){
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



    public static function checkForDoubleChapter($title,$serie){
        $existingSerie = Chapter::where('title', $title)
                                ->where('serie_id',$serie->id)
                                ->first();
        if($existingSerie){
        throw new ChapterAlreadyPresentException("chapter".$title."is already present in serie ".$serie->title);
        }
    }

    public static function validateChapter($title,$serie){
        try {
            self::checkForDoubleChapter($title,$serie);
        } catch (ChapterAlreadyPresentException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
        return true;
    }

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

    //Creates serie
    public function createSerie($chapterCrawler){

        if($this->db==false){
            $info=[];
            $info=$this->addExtraInfo($chapterCrawler);
            echo self::toString($info);
        }
        // if($this->db===true){
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
                // echo "globalSerieCounter: ".++$this->serieCounter."\n";
                //create chapters
                $this->createChapters($chapterCrawler,$serie);
            }
        // }





    }

    protected function toString($array){
        $this->data['author']=$array['serieAuthor'];
        $this->data['artists']=$array['serieArtists'];
        $this->data['publisher']=$array['serieCompany'];
        $this->data['type']=$array['serieType'];
        $this->data['status']=$array['serieStatus'];


        return "Author: " . $this->data['author'] . "\n" .
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
