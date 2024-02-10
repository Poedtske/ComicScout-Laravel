<?php

namespace App\Scraper;
use App\Models\Serie;
use App\Models\Chapter;
use App\Models\Scanlator;
use InvalidArgumentException;
use Symfony\Component\BrowserKit\HttpBrowser;

class FlameComicsScraper extends Scraper{

    protected $src="FlamesComics";
    //filters Constants
    // Constants related to series
    protected $seriesList = '#content > div.wrapper > div.postbody > div.bixbox.seriesearch > div.mrgn > div.listupd > div.bs';
    protected $serieUrl = 'div a';
    protected $serieTitle = 'div a div.bigor div.tt';
    protected $serieCover = 'div a div.limit img';

    // Constants related to URLs
    protected $url = "https://flamecomics.com/series/?page=";

    // Constants related to chapters
    protected $chaptersList = 'div.eplister ul li';
    protected $chapterTitle = 'a div.chbox div.eph-num span.chapternum';
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
     * Checks chapterAmount in a serie in database and in site and adds the newests if necessary
     */

/**
     * Checks  in database and in site and adds the newests if necessary
     */


    public function run() {
        $noSeries=false;
        $pageIndex=1;
        //$requestCounter=0;
        while(!$noSeries){
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
                $serieList->each(function($node) {
                    //add info of serie we can find on the seriesList page
                    $serieUrl = $node->filter($this->serieUrl)->attr('href');
                    $serieTitle = $node->filter($this->serieTitle)->text();
                    $serieCover = $node->filter($this->serieCover)->attr('src');
                    $serieStatus = $node->filter('div a div.bigor div.extra-info div.imptdt div i')->text();

                    $this->data=[
                        'url'=>$serieUrl,
                        'title'=>$serieTitle,
                        'cover'=>$serieCover,
                        'status'=>$serieStatus,
                        ];
                    //go to the specific serie
                    self::requestCooldown();
                    $chapterCrawler = $this->client->request('GET', $serieUrl);

                    //add info of serie we can find on the serieSpecific page
                    $serieInfo = self::addExtraInfo($chapterCrawler);

                    $serieAuthor = $serieInfo['serieAuthor'];
                    $serieArtists = $serieInfo['serieArtists'];
                    $seriePublisher = $serieInfo['serieCompany'];
                    $serieType = $serieInfo['serieType'];
                    $serieSrc = $this->src;
                    //TO DO create serie

                    //create chapters
                    self::createSerie($chapterCrawler);

                });
            }
            $pageIndex++;
        }
    }




    protected function addExtraInfo($chapterCrawler) {
        $info = $chapterCrawler->filter('div.main-info div.second-half div.left-side div div');
        $infoSerie = [];
        $index = 1;
        $info->each(function($node) use (&$infoSerie, &$index) {
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
        $infoSerie['serieGenres'] = $genresArray;
        $infoSerie['serieStatus']=$this->data['status'];

        return $infoSerie;
    }

}
