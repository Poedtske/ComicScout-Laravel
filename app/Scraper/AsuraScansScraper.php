<?php

namespace App\Scraper;
use App\Models\Serie;
use InvalidArgumentException;
use Symfony\Component\BrowserKit\HttpBrowser;
use App\Exceptions\SerieAlreadyPresentException;
use App\Models\Scanlator;

class AsuraScansScraper extends Scraper
{
    protected $src="AsuraScans";
    protected $url='https://asuratoon.com/manga/?page=';
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
            //echo $this->requestCounter;
            self::requestCooldown();
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



                        //go to the specific serie
                        self::requestCooldown();
                        $chapterCrawler = $client->request('GET', $serieLink);

                        //add info of serie we can find on the serieSpecific page
                        $serieInfo = self::addExtraInfo($chapterCrawler);

                        $serieAuthor = $serieInfo['serieAuthor'];
                        $serieArtists = $serieInfo['serieArtists'];
                        $seriePublisher = $serieInfo['serieCompany'];
                        $serieType = $serieInfo['serieType'];
                        $serieSrc = $this->src;
                        $serieDescription=$serieInfo['serieDescription'];
                        $serieStatus = $serieInfo['serieStatus'];
                        $serieGenres = $serieInfo["serieGenres"];

                        //$brotherSeries=self::validate($serieTitle,$serieSrc);

                        $scanlators=Scanlator::all();
                        $scanlator=null;
                        foreach($scanlators as $scan){
                            if($scan->name==$serieSrc){
                                $scanlator=$scan;
                            }
                        }
                        if($scanlator==null){
                            return "Scanlator not valid";
                        }



                        //!!!Right now the serie is being made here, doesn't work yet because description is to long
                        $serie=new Serie();

                        $serie->status=$serieStatus;
                        $serie->title=$serieTitle;
                        $serie->url=$serieLink;
                        $serie->cover=$serieCover;
                        $serie->src=$serieSrc;
                        $serie->author=$serieAuthor;
                        $serie->company=$seriePublisher;
                        $serie->artists=$serieArtists;
                        $serie->type=$serieType;
                        $serie->description=null;
                        $serie->scanlator()->associate($scanlator);

                        $serie->save();


                        echo ++$serieCounter."\n";
                        // echo "\nLink=".$serieLink."\nTitle=".$serieTitle."\nCover=".$serieCover."\nType=".$serieType."\nStatus=".$serieStatus."\nDescription=".$serieDescription;
                        // foreach ($serieGenres as $s) {
                        //     if (is_string($s)) {
                        //         echo $s;
                        //     } else {
                        //         echo "\nGenres=";
                        //         foreach($s as $i){
                        //             echo $i."\n";
                        //         }
                        //     }
                        // };

                        // echo "\nAuthor=".$serieAuthor."\nArtists=".$serieArtists."\nPublisher=".$seriePublisher;

                        //TO DO create serie

                        //create chapters
                        //self::createChapters($chapterCrawler);
                        //sleep(10);

                    });
                }
                catch(InvalidArgumentException){
                    echo "node not found ".$serieCounter;
                }
                // $serieList->each(function($node) use($client,$serieCounter) {
                //     //add info of serie we can find on the seriesList page
                //     $serieLink = $node->filter('div a')->attr('href');
                //     $serieTitle = $node->filter('div a div.bigor div.tt')->text();
                //     $serieCover = $node->filter('div a div.limit img')->attr('src');



                //     //go to the specific serie
                //     self::requestCooldown();
                //     $chapterCrawler = $client->request('GET', $serieLink);

                //     //add info of serie we can find on the serieSpecific page
                //     $serieInfo = self::addExtraInfo($chapterCrawler);

                //     $serieAuthor = $serieInfo['serieAuthor'];
                //     $serieArtists = $serieInfo['serieArtists'];
                //     $seriePublisher = $serieInfo['serieCompany'];
                //     $serieType = $serieInfo['serieType'];
                //     $serieSrc = $this->src;
                //     $serieDescription=$serieInfo['serieDescription'];
                //     $serieStatus = $serieInfo['serieStatus'];
                //     $serieGenres = $serieInfo["serieGenres"];

                //     //$brotherSeries=self::validate($serieTitle,$serieSrc);

                //     // $scanlators=Scanlator::all();
                //     // $scanlator=null;
                //     // foreach($scanlators as $scan){
                //     //     if($scan->name==$serieSrc){
                //     //         $scanlator=$scan;
                //     //     }
                //     // }
                //     // if($scanlator==null){
                //     //     return "Scanlator not valid";
                //     // }



                //     //!!!Right now the serie is being made here, doesn't work yet because description is to long
                //     // $serie=new Serie();

                //     // $serie->status=$serieStatus;
                //     // $serie->title=$serieTitle;
                //     // $serie->url=$serieLink;
                //     // $serie->cover=$serieCover;
                //     // $serie->src=$serieSrc;
                //     // $serie->author=$serieAuthor;
                //     // $serie->company=$seriePublisher;
                //     // $serie->artists=$serieArtists;
                //     // $serie->type=$serieType;
                //     // $serie->description=null;
                //     // $serie->scanlator()->associate($scanlator);

                //     // $serie->save();


                //     // echo ++$serieCounter."\n";
                //     // echo "\nLink=".$serieLink."\nTitle=".$serieTitle."\nCover=".$serieCover."\nType=".$serieType."\nStatus=".$serieStatus."\nDescription=".$serieDescription;
                //     // foreach ($serieGenres as $s) {
                //     //     if (is_string($s)) {
                //     //         echo $s;
                //     //     } else {
                //     //         echo "\nGenres=";
                //     //         foreach($s as $i){
                //     //             echo $i."\n";
                //     //         }
                //     //     }
                //     // };

                //     // echo "\nAuthor=".$serieAuthor."\nArtists=".$serieArtists."\nPublisher=".$seriePublisher;

                //     //TO DO create serie

                //     //create chapters
                //     //self::createChapters($chapterCrawler);
                //     //sleep(10);

                // });
            }
            $pageIndex++;
        }

    }

    //Creates chapters but not yet implemented
    protected static function createChapters($chapterCrawler) {
        $chapterList = $chapterCrawler->filter('#chapterlist ul li');
        $chapterList->each(function($node) {
            $chapterName = $node->filter('div  div  a  span.chapternum')->text();
            $chapterUrl = $node->filter('a')->attr('href');
            //TO DO create chapter
        });
    }

    protected static function addExtraInfo($chapterCrawler) {
        $infoSerie = [];
        //echo $chapterCrawler->html();
        $info = $chapterCrawler->filter('div.bixbox.animefull div.bigcontent.nobigcover div.thumbook div.rt div.tsinfo div.imptdt');
        $info->each(function($node,$index=1) use (&$infoSerie ) {

        //echo $node->text();
        if (strpos($node->text(), "Type") !== false) {
            $infoSerie['serieType'] = $node->filter('a')->text();
        } elseif (strpos($node->text(), 'Status') !== false) {
            $infoSerie['serieStatus'] = $node->filter('i')->text();
        }
        });

        //description  search for synopsis
        //zoek nr synopsis en Genre
        $genresArray = [];
        $info = $chapterCrawler->filter('div.bixbox.animefull div.bigcontent.nobigcover div.infox div.wd-full');
        $info->each(function($node,) use (&$infoSerie, &$genresArray ) {
            if (strpos($node->text(), "Synopsis") !== false) {
                $infoSerie['serieDescription'] = $node->filter('div p')->text();
            } elseif (strpos($node->text(), 'Genres') !== false) {
                $genresArray[] = $node->filter('span a')->each(function ($node){
                    return $node->text();
                });
            }
            });

        //Author, Publisher and Artists
        $info = $chapterCrawler->filter('div.bixbox.animefull div.bigcontent.nobigcover div.infox div.flex-wrap');
        $hasArtist=false;
        if(strpos($info->text(),'Artist')){
            $hasArtist=true;
        }
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
                        else{
                            $infoSerie["serieArtists"]= "N/A";
                        }
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

    //!!!Not implemented right now, starts the creation of the series
    public function createSerie($serieTitle,$src){
        $brotherSeries=self::validate($serieTitle,$src);
        $scanlators=Scanlator::all();
        $scanlator=null;
        foreach($scanlators as $scan){
            if($scan->name==$src){
                $scanlator=$scan;
            }
        }
        if($scanlator==null){
            return "Scanlator not valid";
        }
        //$serie=Serie::create($request)->scanlator()->associate($scanlator);

        //validated returns something similar as below
        //$post=Post::create($validated);
        //this is done by create, Mass Assignment
        //$post=new Post();
        //$post->title=$request->input('title');
        //$post->description=$request->input('description');
        //$post->save();
    }

    //!!!Not implemented right now,checks for doubles and same serie in other scanlators
    private function checkForDouble($title,$scanlator){
        $series=Serie::all();
        $brotherSeries=[];
        foreach($series as $serie){
            if ($serie->title==$title&&$serie->scanlator_id=$scanlator){
                // throw new Error();
            }
            else{
                if($serie->title==$title)
                $brotherSeries[]=$serie->id;
            }
        }
        if(empty($brotherSeries)){
            return false;
        }
        else{
            return $brotherSeries;
        }
    }
    //Not implemented right now, validation for serie
    private function validate($serieTitle,$src){
        $title=$serieTitle;
        $scanlator=$src;
        $brotherSeries=self::checkForDouble($title,$scanlator);
        if($brotherSeries==false){
            throw new SerieAlreadyPresentException("serie".$title."is already present in scanlator".$scanlator);
        }
        else{
            return$brotherSeries;
        }
    }
}
