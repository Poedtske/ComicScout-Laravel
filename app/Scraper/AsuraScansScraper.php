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
    //name of the source where the Scraper scrapes from
    protected $src="AsuraScans";


    //filters Constants
    // Selectors related to series
    protected $seriesList = 'div.listupd div.bs';
    protected $serieUrl = 'div a';
    protected $serieTitle = 'div a div.bigor div.tt';
    protected $serieCover = 'div a div.limit img';

    // Selector related to URLs
    protected $url = 'https://asuratoon.com/manga/?page=';

    // Selectors related to chapters
    protected $chaptersList = '#chapterlist ul li';
    protected $chapterTitle = 'div div a span.chapternum';
    protected $chapterUrl = 'a';

    public function __construct() {
        parent::__construct($this->db);

    }



    /**
     * Updates domain name of Scanlater if there is a need to change it
     */
    public function updateDomain($newDomainName){

    }



    /**
     * starts the process of scraping the series and chapters and adding them to the database
     */
    public function run() {

        $noSeries=false;
        $pageIndex=1;
        $serieCounter=0;
        while(!$noSeries){
            // //echo $this->requestCounter;

            self::requestCooldown();
            $pageCrawler = $this->client->request('GET', $this->url.strval($pageIndex));
            $serieList = $pageCrawler->filter($this->seriesList);
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
                        $serieUrl = $node->filter($this->serieUrl)->attr('href');
                        $serieTitle = $node->filter($this->serieTitle)->text();
                        $serieCover = $node->filter($this->serieCover)->attr('src');
                        $serieSrc = $this->src;


                        //go to the specific serie
                        self::requestCooldown();
                        $chapterCrawler = $this->client->request('GET', $serieUrl);

                        //add info of serie we can find on the serieSpecific page




                        //$brotherSeries=self::validate($serieTitle,$serieSrc);
                        $this->data=[
                        'url'=>$serieUrl,
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
                            $this->createSerie($chapterCrawler);








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




    /**
     * adds extra info, is specific to the site
     */
    protected function addExtraInfo($chapterCrawler) {
        $infoSerie = [];
        //echo $chapterCrawler->html();
        $info = $chapterCrawler->filter('div.bixbox.animefull div.bigcontent div.thumbook div.rt div.tsinfo div.imptdt');
        try{
            $info->each(function($node) use (&$infoSerie ) {

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
