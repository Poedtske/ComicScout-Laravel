<?php

namespace App\Scraper;
use App\Models\Serie;
use App\Models\Chapter;
use App\Models\Scanlator;
use InvalidArgumentException;
use App\Exceptions\InvalidScanlatorException;
use Symfony\Component\BrowserKit\HttpBrowser;
use App\Exceptions\SerieAlreadyPresentException;
use App\Exceptions\ChapterAlreadyPresentException;

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


    public function run() {

        $noSeries=false;
        $pageIndex=1;
        $serieCounter=0;
        while(!$noSeries){
            // //echo $this->requestCounter;

            //self::requestCooldown();
            $pageCrawler = $this->client->request('GET', $this->url.strval($pageIndex));
            $serieList = $pageCrawler->filter('div.listupd div.bs');
            try {
                    $serieList->text();
                } catch (InvalidArgumentException) {
                    $noSeries=true;
                    echo "no series on this page";
                }
            if(!$noSeries){
                try{
                    $serieList->each(function($node) use(&$serieCounter) {
                        //add info of serie we can find on the seriesList page
                        $serieLink = $node->filter('div a')->attr('href');
                        $serieTitle = $node->filter('div a div.bigor div.tt')->text();
                        $serieCover = $node->filter('div a div.limit img')->attr('src');
                        $serieSrc = $this->src;


                        //go to the specific serie
                        //self::requestCooldown();
                        $chapterCrawler = $this->client->request('GET', $serieLink);

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
                            $this->createSerie($chapterCrawler);

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



    protected function createChapters($chapterCrawler,$serie) {
        $chapterList = $chapterCrawler->filter('#chapterlist ul li');
        $chapterArray=[];

        $chapterList->each(function($node) use($serie,&$chapterArray) {
            $chapterTitle = $node->filter('div  div  a  span.chapternum')->text();
            $chapterUrl = $node->filter('a')->attr('href');
            $chapter=['title'=>$chapterTitle,
                'url'=>$chapterUrl,
        ];
        // echo $chapter['title'];

            $allowed=self::validateChapter($chapterTitle,$serie);
            if($allowed){
                $chapterArray[]=$chapter;
            }


        });
        //echo count($chapterArray);
        $chapterArray=array_reverse($chapterArray);
        foreach ($chapterArray as $chap) {
            $chapter= new Chapter();
                $chapter->title=$chap['title'];
                $chapter->url=$chap['url'];
                $chapter->serie()->associate($serie);
                $chapter->save();
        };


    }

    protected function addExtraInfo($chapterCrawler) {
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



}
