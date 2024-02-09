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
    protected $url=null;
    protected $src="Scraper";
    protected $requestCounter;
    protected $counter=0;
    protected $client;
    protected $data=[];

    public function __construct() {
        $this->requestCounter=0;
        $this->client = new HttpBrowser();
    }
    protected abstract function createChapters($chapterCrawler,$serie);

    protected abstract function addExtraInfo($chapterCrawler);


    public function requestCooldown()
    {

        //echo $this->requestCounter;
        if($this->requestCounter>=10){
            //echo 'going to sleep';
            $this->counter++;
            // echo $this->counter;
            sleep(30);
            $this->requestCounter=0;
        }
        else{
            $this->requestCounter++;
        }
    }

    public function serieUpdater(){
        $scanlator=Scanlator::where("name",$this->src)->first();
        Serie::where('scanlator_id',$scanlator->id)->get()->where('status','Ongoing')->each(function($serie){
            $chapterCrawler=$this->client->request('GET', $serie->url);
            if(count($chapterCrawler->filter('#chapterlist ul li'))==count($serie->chapters)){
                return;
            }
            else{
                self::createChapters($chapterCrawler,$serie);
            }
        });
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

        $result=self::validateSerie($chapterCrawler);
        $scanlator=$result['scanlator'];
        $allowed=$result['allowed'];


        // $existingSerieName = Serie::where('title', $data['title'])
        // ->get();




        if($allowed){
            $serie=new Serie();

            $serie->status=$this->data['status'];
            $serie->title=$this->data['title'];
            $serie->url=$this->data['link'];
            $serie->cover=$this->data['cover'];
            $serie->author=$this->data['author'];
            $serie->company=$this->data['publisher'];
            $serie->artists=$this->data['artists'];
            $serie->type=$this->data['type'];
            $serie->description=null;
            $serie->scanlator()->associate($scanlator);
            $serie->save();

            //create chapters
            $this->createChapters($chapterCrawler,$serie);
        }

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
