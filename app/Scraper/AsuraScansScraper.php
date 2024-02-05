<?php

namespace App\Scraper;
use Symfony\Component\BrowserKit\HttpBrowser;
use InvalidArgumentException;

class AsuraScansScraper extends Scraper
{
    public function __construct($url,$src) {
        parent::__construct($url,$src);
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
                $serieList->each(function($node) use($client) {
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

                    //echo "\nLink=".$serieLink."\nTitle=".$serieTitle."\nCover=".$serieCover."\nType=".$serieType."\nStatus=".$serieStatus."\nDescription=".$serieDescription.";
                    // foreach ($serieGenres as $s) {
                    //     if (is_string($s)) {
                    //         echo $s;
                    //     } else {
                    //         echo "\nGenres=";
                    //         foreach($s as $i){
                    //             echo $i;
                    //         }
                    //     }
                    // };

                    //echo "\nAuthor=".$serieAuthor."\nArtists=".$serieArtists."\nPublisher=".$seriePublisher;

                    //TO DO create serie

                    //create chapters
                    self::createChapters($chapterCrawler);
                    //sleep(10);

                });
            }
            $pageIndex++;
        }

    }

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
        $info->each(function($node,) use (&$infoSerie) {
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

            if (strpos($node->text(), 'Artist') !== false) {
                $node->filter('div.fmed')->each(function($node)use(&$infoSerie){
                    if($node->filter('b')->text()=="Artist"){
                        $infoSerie["serieArtists"]= $node->filter('span')->text();
                    }
                });
            }
            else{
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
}
