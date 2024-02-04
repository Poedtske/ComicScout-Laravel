<?php

namespace App\Scraper;
use Symfony\Component\BrowserKit\HttpBrowser;
use InvalidArgumentException;

class FlameComicsScraper extends Scraper{

    public function __construct($url,$src) {
        parent::__construct($url,$src);
        echo $this->requestCounter;
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
        //$requestCounter=0;
        while(!$noSeries){
            self::requestCooldown();
            $pageCrawler = $client->request('GET', $this->url.strval($pageIndex));
            $serieList = $pageCrawler->filter('#content > div.wrapper > div.postbody > div.bixbox.seriesearch > div.mrgn > div.listupd > div.bs');
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
                    $serieCover = $node->filter('div a div.limit img')->text();
                    $serieStatus = $node->filter('div a div.bigor div.extra-info div.imptdt div i')->text();

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
                    //TO DO create serie

                    //create chapters
                    self::createChapters($chapterCrawler);

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
        $info = $chapterCrawler->filter('div.main-info div.second-half div.left-side div div');
        $infoSerie = [];
        $index = 1;
        $info->each(function($node) use (&$infoSerie, &$index, $chapterCrawler) {
            switch ($index) {
                case 1:
                    $infoSerie['serieType'] = $node->filter('i')->text();
                    break;
                case 4:
                    $infoSerie['serieAuthor'] = $node->filter('i')->text();
                    break;
                case 5:
                    $infoSerie['serieArtists'] = $node->filter('i')->text();
                    break;
                case 6:
                    $infoSerie['serieCompany'] = $node->filter('i')->text();
                    break;
            }
            $index++;
        });

        // Extracting genres
        $genres = $chapterCrawler->filter('div.main-info div.first-half div.info-half div.genres-container div span a');
        $genresArray = [];
        $genres->each(function($node) use (&$genresArray) {
            $genresArray[] = $node->text();
        });

        // Adding genres to infoSerie array
        $infoSerie['genres'] = $genresArray;

        return $infoSerie;
    }
}
