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

                    // $serieAuthor = $serieInfo['serieAuthor'];
                    // $serieArtists = $serieInfo['serieArtists'];
                    // $seriePublisher = $serieInfo['serieCompany'];
                    $serieType = $serieInfo['serieType'];
                    $serieSrc = $this->src;
                    $serieDescription=$serieInfo['serieDescription'];
                    $serieStatus = $serieInfo['serieStatus'];

                    //echo "\nLink=".$serieLink."\nTitle=".$serieTitle."\nCover=".$serieCover."\nType=".$serieType."\nStatus=".$serieStatus."\nDescription=".$serieDescription."\nGenres=".$serieInfo["serieGenres"];
                    //echo $serieDescription;
                    $array = $serieInfo["serieGenres"];
foreach ($array as $s) {
    if (is_string($s)) {
        echo $s;
    } else {
        foreach($s as $i){
            echo $i;
        }
    }
}

                    sleep(10);
                    // Author=$serieAuthor
                    // Artists=$serieArtists
                    // Publisher=$seriePublisher
                    // Type=$serieType
                    // Src=$this->src
                    // Description=$serieDescription`;
                    //TO DO create serie

                    //create chapters
                    //self::createChapters($chapterCrawler);

                });
            }
            $pageIndex++;
        }

    }

    protected static function createChapters($chapterCrawler) {
        $chapterList = $chapterCrawler->filter('div.eplister ul li');
        $chapterList->each(function($node) {
            $chapterName = $node->filter('a div.chbox div.eph-num span.chapternum')->text();
            $chapterUrl = $node->filter('a')->attr('href');
            echo $chapterName;
            echo $chapterUrl;
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
        $info->each(function($node,$index=1) use (&$infoSerie, &$genresArray ) {

            //echo $node->text();
            if (strpos($node->text(), "Synopsis") !== false) {
                $infoSerie['serieDescription'] = $node->filter('div p')->text();
            } elseif (strpos($node->text(), 'Genres') !== false) {
                $genresArray[] = $node->filter('span a')->each(function ($node){
                    return $node->text();
                });
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
