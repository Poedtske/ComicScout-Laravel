<?php

namespace App\Scraper;
use App\Models\Serie;
use App\Models\Chapter;
use App\Models\Scanlator;
use InvalidArgumentException;
use App\Exceptions\InvalidScanlatorException;
use Symfony\Component\BrowserKit\HttpBrowser;
use App\Exceptions\SerieAlreadyPresentException;

class AsuraScansScraper extends Scraper
{
    protected $src="AsuraScans";
    protected $url='https://asuratoon.com/manga/?page=';
    protected $data=[];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Updates domain name of Scanlater if there is a need to change it
     */
    public function updateDomain($newDomainName){

    }



    /**
     * Checks chapterAmount in a serie in database and in site and adds the newests if necessary
     */
    public function chapterUpdater(){

    }
/**
     * Checks  in database and in site and adds the newests if necessary
     */
    public function serieUpdater(){

    }

    public function run() {
        $client = new HttpBrowser();
        $noSeries=false;
        $pageIndex=1;
        $serieCounter=0;
        while(!$noSeries){
            // //echo $this->requestCounter;

            //self::requestCooldown();
            $pageCrawler = $client->request('GET', $this->url.strval($pageIndex));
            $serieList = $pageCrawler->filter('div.listupd div.bs');
            try {
                    $serieList->text();
                } catch (InvalidArgumentException) {
                    $noSeries=true;
                    echo "no series on this page";
                }
            if(!$noSeries){
                try{
                    $serieList->each(function($node) use($client,&$serieCounter) {
                        //add info of serie we can find on the seriesList page
                        $serieLink = $node->filter('div a')->attr('href');
                        $serieTitle = $node->filter('div a div.bigor div.tt')->text();
                        $serieCover = $node->filter('div a div.limit img')->attr('src');
                        $serieSrc = $this->src;


                        //go to the specific serie
                        //self::requestCooldown();
                        $chapterCrawler = $client->request('GET', $serieLink);

                        //add info of serie we can find on the serieSpecific page




                        //$brotherSeries=self::validate($serieTitle,$serieSrc);
                        $this->data=[
                        'link'=>$serieLink,
                        'title'=>$serieTitle,
                        'cover'=>$serieCover,
                        ];

                        // 'author' => $serieInfo['serieAuthor'],
                        // 'artists' => $serieInfo['serieArtists'],
                        // 'publisher' => $serieInfo['serieCompany'],
                        // 'type' => $serieInfo['serieType'],
                        // 'src' => $this->src,
                        // 'status' => $serieInfo['serieStatus'],

                        //call function to create serie & its chapters
                        $db=true;
                        if($db){
                            self::createSerie($chapterCrawler);

                        }

                        // $sout=false;
                        // if($sout){
                        //     echo "\nLink=".$serieLink."\nTitle=".$serieTitle."\nCover=".$serieCover."\nType=".$serieType."\nStatus=".$serieStatus
                        //     //."\nDescription=".$serieDescription
                        //     ;
                        //     foreach ($serieGenres as $s) {
                        //         if (is_string($s)) {
                        //             echo $s;
                        //         } else {
                        //             echo "\nGenres=";
                        //             foreach($s as $i){
                        //                 echo $i."\n";
                        //             }
                        //         }
                        // };

                        // echo "\nAuthor=".$serieAuthor."\nArtists=".$serieArtists."\nPublisher=".$seriePublisher;
                        // }







                        echo ++$serieCounter."\n";




                    });
                }
                catch(InvalidArgumentException){
                    echo "node not found 1".$serieCounter;
                }
            }
            $pageIndex++;
        }

    }

    //Creates chapters
    protected static function createChapters($chapterCrawler,$serie) {
        $chapterList = $chapterCrawler->filter('#chapterlist ul li');
        $chapterList->each(function($node) use($serie) {
            $chapterTitle = $node->filter('div  div  a  span.chapternum')->text();
            $chapterUrl = $node->filter('a')->attr('href');
            //TO DO create chapter
            $chapter= new Chapter();
            $chapter->title=$chapterTitle;
            $chapter->url=$chapterUrl;
            $chapter->serie()->associate($serie);
            $chapter->save();
        });
    }

    protected static function addExtraInfo($chapterCrawler) {
        $infoSerie = [];
        //echo $chapterCrawler->html();
        $info = $chapterCrawler->filter('div.bixbox.animefull div.bigcontent div.thumbook div.rt div.tsinfo div.imptdt');
        try{
            $info->each(function($node,$index=1) use (&$infoSerie ) {

                //echo $node->text();
                if (strpos($node->text(), "Type") !== false) {
                    $infoSerie['serieType'] = $node->filter('a')->text();
                } elseif (strpos($node->text(), 'Status') !== false) {
                    $infoSerie['serieStatus'] = $node->filter('i')->text();
                }
                });
        }
        catch(InvalidArgumentException){
            echo "node not found 2";
        }


        //description  search for synopsis
        //zoek nr synopsis en Genre
        $genresArray = [];

        $info = $chapterCrawler->filter('div.bixbox.animefull div.bigcontent div.infox div.wd-full');
        try{
            $info->each(function($node,) use (&$infoSerie, &$genresArray ) {
                try{
                    // if (strpos($node->text(), "Synopsis") !== false) {
                    //     $infoSerie['serieDescription'] = $node->filter('div p')->text();
                    // } else
                    if (strpos($node->text(), 'Genres') !== false) {
                        $genresArray[] = $node->filter('span a')->each(function ($node){
                            return $node->text();
                        });
                    }
                }catch(InvalidArgumentException){
                    echo "node not found: Genres";
                    $node->html();
                }

                });
        }catch(InvalidArgumentException){
            echo "node not found 3.1";
        }

        try{

        }catch(InvalidArgumentException){

        }


        //Author, Publisher and Artists
        $info = $chapterCrawler->filter('div.bixbox.animefull div.bigcontent div.infox div.flex-wrap');
        $hasArtist=false;
        if(strpos($info->text(),'Artist')){
            $hasArtist=true;
        }
        try{
            $info->each(function($node,) use (&$infoSerie,$hasArtist) {
                if($hasArtist){
                    if (strpos($node->text(), "Author") !== false) {
                        $node->filter('div.fmed')->each(function($node,$indexL=1)use(&$infoSerie){
                            if($node->filter('b')->text()=="Author"){
                                $infoSerie["serieAuthor"]= $node->filter('span')->text();
                            }
                        });
                    } elseif (strpos($node->text(), 'Serialization') !== false) {
                        $node->filter('div.fmed')->each(function($node)use(&$infoSerie){
                            if($node->filter('b')->text()=="Serialization"){

                                $infoSerie["serieCompany"]= $node->filter('span')->text();
                            }
                        });
                    }
                    elseif (strpos($node->text(), 'Artist') !== false) {
                        $node->filter('div.fmed')->each(function($node)use(&$infoSerie){
                            if($node->filter('b')->text()=="Artist"){
                                $infoSerie["serieArtists"]= $node->filter('span')->text();
                            }
                            // else{
                            //     $infoSerie["serieArtists"]= "N/A";
                            // }
                        });
                    }
                }else{
                    if (strpos($node->text(), "Author") !== false) {
                        $node->filter('div.fmed')->each(function($node,$indexL=1)use(&$infoSerie){
                            if($node->filter('b')->text()=="Author"){
                                $infoSerie["serieAuthor"]= $node->filter('span')->text();
                            }
                        });
                    } elseif (strpos($node->text(), 'Serialization') !== false) {
                        $node->filter('div.fmed')->each(function($node)use(&$infoSerie){
                            if($node->filter('b')->text()=="Serialization"){

                                $infoSerie["serieCompany"]= $node->filter('span')->text();
                            }
                        });
                    }
                    $infoSerie["serieArtists"]= "N/A";
                }

                });
        }catch(InvalidArgumentException){
            echo "node not found 4";
        }




        // // Extracting genres
        // $genres = $chapterCrawler->querySelector("div.infox div:nth-child(7) span a");
        // $genresArray = [];
        // $genres->each(function($node) use (&$genresArray) {
        //     $genresArray[] = $node->text();
        // });

        // Adding genres to infoSerie array
        $infoSerie['serieGenres'] = $genresArray;

        return $infoSerie;
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
            self::checkForDouble($this->data['title'],$scanlator);
        } catch (SerieAlreadyPresentException $e) {
            return ['allowed'=>false,
            'scanlator'=>$scanlator];
        }

        $serieInfo = self::addExtraInfo($chapterCrawler);

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
            self::createChapters($chapterCrawler,$serie);
        }

    }

    //checks if the scanlator exists in the databank
    private function checkScanlator(){
        $scanlator=Scanlator::where('name',$this->src)->first();
        if($scanlator===null){
            throw new InvalidScanlatorException("This scanlator".$this->src."does not exist");
        }
        else{
            return $scanlator;
        }
    }

    //checks if there is a serie with the same name and scanlator
    private function checkForDouble($title,$scanlator){
        $existingSerie = Serie::where('title', $title)
                            ->where('scanlator_id', $scanlator->id)
                            ->first();
        if($existingSerie){
            throw new SerieAlreadyPresentException("serie".$title."is already present in scanlator".$scanlator);
        }
    }
}
