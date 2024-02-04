<?php

require 'vendor/autoload.php';


use Symfony\Component\BrowserKit\HttpBrowser;

class Scraper {
    private $url;
    private $src;

    public function __construct($url,$src) {
        $this->url=$url;
        $this->src=$src;
    }

    public function run() {
        $client = new HttpBrowser();
        $noSeries=false;
        $index=1;
        while(!$noSeries){
            $pageCrawler = $client->request('GET', $this->url.strval($index));
                $serieList = $pageCrawler->filter('#content > div.wrapper > div.postbody > div.bixbox.seriesearch > div.mrgn > div.listupd > div.bs');
            try {
                    $serieList->text();
                } catch (InvalidArgumentException) {
                    $noSeries=true;
                    echo "no series on this page";
                }
            if(!$noSeries){
                $serieList->each(function($node) {
                    $serieLink = $node->filter('div a')->attr('href');
                    $serieTitle = $node->filter('div a div.bigor div.tt')->text();
                    $serieCover = $node->filter('div a div.limit img')->text();
                    $serieStatus = $node->filter('div a div.bigor div.extra-info div.imptdt div i')->text();
                    $serieInfo = self::addExtraInfo($serieLink);
                    $serieAuthor = $serieInfo['serieAuthor'];
                    $serieArtists = $serieInfo['serieArtists'];
                    $seriePublisher = $serieInfo['serieCompany'];
                    $serieType = $serieInfo['serieType'];
                    $serieSrc = $this->src;
                    self::createChaptors($serieLink);
                });
            }
        }

    }

    private static function createChaptors($serieLink) {
        $client = new HttpBrowser();
        $chapterCrawler = $client->request('GET', $serieLink);
        $chapterList = $chapterCrawler->filter('div.eplister ul li');
        $chapterList->each(function($node) {
            $chapterName = $node->filter('a div.chbox div.eph-num span.chapternum')->text();
            $chapterUrl = $node->filter('a')->attr('href');
            echo $chapterName;
            echo $chapterUrl;
        });
    }

    private static function addExtraInfo($serieLink) {
        $client = new HttpBrowser();
        $chapterCrawler = $client->request('GET', $serieLink);
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

$s=new Scraper("https://flamecomics.com/series/?page=1","FlameComics");
$s->run();


//echo $serieList->html();



