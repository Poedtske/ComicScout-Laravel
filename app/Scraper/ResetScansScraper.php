<?php
namespace App\Scraper;

use App\Scraper\Scraper;
use InvalidArgumentException;

class ResetScansScraper extends Scraper
{
    protected $src="ResetScans";
    //filters Constants
    // Constants related to series
    protected $seriesList = 'body div.wrap div div.site-content div.c-page-content.style-1 div div div div div.main-col-inner div div.c-page__content div.tab-content-wrap div div div.page-listing-item';
    protected $extraLoop='div div';
    // 'body > div.wrap > div > div.site-content > div.c-page-content.style-1 > div > div > div > div > div.main-col-inner > div > div.c-page__content > div.tab-content-wrap > div > div > div:nth-child(1) > div > div:nth-child(1)'
    //'body > div.wrap > div > div.site-content > div.c-page-content.style-1 > div > div > div > div > div.main-col-inner > div > div.c-page__content > div.tab-content-wrap > div > div > div:nth-child(1) > div > div:nth-child(2) > div > div.item-summary > div.post-title.font-title > h3 > a'
    // 'body > div.wrap > div > div.site-content > div.c-page-content.style-1 > div > div > div > div > div.main-col-inner > div > div.c-page__content > div.tab-content-wrap > div > div > div:nth-child(2)'

    protected $serieUrl = 'div div.item-summary div.post-title.font-title h3 a';
    protected $serieTitle = 'div div.item-summary div.post-title.font-title h3 a';
    protected $serieCover = 'div div.item-thumb a img';

    // Constants related to URLs
    protected $url = "https://reset-scans.us/mangas/page/";

    // Constants related to chapters
    protected $chaptersList = '#manga-chapters-holder div.page-content-listing.single-page div ul li';
    protected $chapterTitle = 'div.li__text a';
    protected $chapterUrl = 'div.li__text a';

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
                    $node->filter('div div.col-12')->each(function($serieNode) { // Use a different variable name for clarity
                        //add info of serie we can find on the seriesList page
                        $serieUrl = $serieNode->filter($this->serieUrl)->attr('href');
                        $serieTitle = $serieNode->filter($this->serieTitle)->text();
                        $serieCover = $serieNode->filter($this->serieCover)->attr('src');
                        if($serieCover=='https://reset-scans.us/wp-content/uploads/2022/11/LIBRARY-OF-HEAVENS-PATH-110x150.webp'){
                            $serieUrl='https://reset-scans.us/manga/library-of-heavens-path/';
                            $serieTitle="Library Of Heaven's Path";
                        }
                        if($serieCover=='https://reset-scans.us/wp-content/uploads/2022/07/Asterisk-The-Dragon-110x150.webp'){
                            $serieUrl='https://reset-scans.us/manga/dragon-walking-on-the-milky-way/';
                            $serieTitle="Asterisk The Dragon Walking on the Milky Way";
                        }
                        $this->data=[
                            'url'=>$serieUrl,
                            'title'=>$serieTitle,
                            'cover'=>$serieCover,
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
                });
            }
            $pageIndex++;
        }
    }




    protected function addExtraInfo($chapterCrawler) {
        $info = $chapterCrawler->filter('#nav-info div div.post-content div.post-content_item');
        $infoSerie = [];
        $index = 1;
        $authorPresent=false;
        $artistPresent=false;
        $statusPresent=false;
        $genresPresent=false;

        $info->each(function($node) use (&$infoSerie, &$index,&$authorPresent,&$artistPresent,&$statusPresent,&$genresPresent) {
            if(!$authorPresent){
                if (strpos($node->filter('div')->eq(1)->filter("h5")->text(), "Author(s)") !== false) {
                    $authorNames = $node->filter('div')->eq(2)->filter("div a")->each(function ($authorNode) {
                        return $authorNode->text();
                    });
                    $infoSerie["serieAuthor"]=implode(', ', $authorNames);
                    $authorPresent=true;
                }
            }
            if(!$artistPresent){
                if (strpos($node->filter('div')->eq(1)->filter("h5")->text(), 'Artist(s)') !== false) {
                    $artistNames = $node->filter('div')->eq(2)->filter("div a")->each(function ($artistNode) {
                        return $artistNode->text();
                    });
                    $infoSerie["serieArtists"]=implode(', ', $artistNames);
                    $artistPresent=true;
                }
            }
            if($genresPresent){
                if (strpos($node->filter('div')->eq(1)->filter("h5")->text(), 'Genre(s)') !== false) {
                    $genres = $node->filter('div')->eq(2)->filter("div a")->each(function ($statusNode) {
                        return $statusNode->text();
                    });
                    $infoSerie["serieGenres"]=implode(', ', $genres);
                    $statusPresent=true;
                }
            }
        });

        $info = $chapterCrawler->filter('#nav-info div div.post-status div.post-content_item');
        $info->each(function($node) use (&$infoSerie,&$statusPresent) {
            if(!$statusPresent){
                if (strpos($node->filter('div')->eq(1)->filter("h5")->text(), 'Status') !== false) {
                    $status = $node->filter('div')->eq(2)->filter("div")->each(function ($statusNode) {
                        return $statusNode->text();
                    });
                    $infoSerie["serieStatus"]=implode(', ', $status);
                    $statusPresent=true;
                }
            }
        });

        if(!$authorPresent){
            $infoSerie["serieAuthor"]='N/A';
        }
        if(!$artistPresent){
            $infoSerie["serieArtists"]='N/A';
        }
        if(!$statusPresent){
            $infoSerie['serieStatus']='N/A';
        }
        if(!$genresPresent){
            $infoSerie['serieGenres']='N/A';
        }

        // Extracting genres
        // $genres = $chapterCrawler->filter('#nav-info div div.post-content div')->eq(8)->filter('div.summary-content div a');
        // $genresArray = [];
        // $genres->each(function($node) use (&$genresArray) {
        //     $genresArray[] = $node->text();
        // });

        // $infoSerie['serieStatus']=$chapterCrawler->filter('#nav-info div div.post-status div')->eq(2)->filter('div.summary-content')->text();


        // Adding genres to infoSerie array
        // $infoSerie['serieGenres'] = $genresArray;
        $infoSerie['serieCompany']='N/A';
        $infoSerie['serieType']='N/A';

        return $infoSerie;
    }
}
