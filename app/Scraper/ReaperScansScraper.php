<?php
namespace App\Scraper;

use App\Models\Chapter;
use InvalidArgumentException;

class ReaperScansScraper extends Scraper
{
    //name of the source where the Scraper scrapes from
    protected $src="ReaperScans";
    //filters Constants
    //Selectors related to series
    protected $seriesList = 'body div.flex.flex-col.h-screen.justify-between main div div div div.mt-6.grid.grid-cols-2.gap-4 li';
    protected $serieUrl = 'div  a.relative.transition';
    protected $serieTitle = 'div  a.my-2.text-sm.font-medium.text-white';
    protected $serieCover = 'div  a.relative.transition img';

    //Selector related to URLs
    protected $url = "https://reaperscans.com/comics?page=";

    //Selectos related to chapters
    protected $chaptersList = 'body  div.flex.flex-col.h-screen.justify-between main div div div.max-w-6xl.bg-white.rounded.mt-6  div.pb-4  div  div  ul li';
    protected $chapterTitle = 'a  div  div.min-w-0.flex-1 div div.flex.text-sm p';
    protected $chapterUrl = 'a';

    protected $requestMaxBeforeCooldown=2;//edits amount of request/min to prevent being blocked

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

                    $this->data=[
                        'url'=>$serieUrl,
                        'title'=>$serieTitle,
                        'cover'=>$serieCover,
                        ];
                    //go to the specific serie
                    self::requestCooldown();
                    $chapterCrawler = $this->client->request('GET', $serieUrl);

                    //create chapters
                    self::createSerie($chapterCrawler);

                });
            }
            $pageIndex++;
        }
    }

    /**
     * is process to add chapters, uses methodhiding to override the one of the parent class
     * is used because the website needed an unique approach
     */
    protected function createChapters($chapterCrawler,$serie) {

        $noChapters=false;
        $pageIndex=1;
        $chapterArray=[];
        //$requestCounter=0;
        while(!$noChapters){
            self::requestCooldown();
            $pageChapterCrawler = $this->client->request('GET', $serie->url.'?page='.strval($pageIndex));
            $chapterList = $pageChapterCrawler->filter($this->chaptersList);
            try {
                    $chapterList->text();
                } catch (InvalidArgumentException) {
                    $noChapters=true;
                    echo "no chapters on this page";
                }
            if(!$noChapters){
                $chapterList->each(function($node) use($serie,&$chapterArray)  {
                    $chapterTitle = $node->filter($this->chapterTitle)->text();
                    $chapterUrl = $node->filter($this->chapterUrl)->attr('href');
                    $chapter=['title'=>$chapterTitle,
                    'url'=>$chapterUrl,];

                    $allowed=self::validateChapter($chapterTitle,$serie);
                    if($allowed){
                        $chapterArray[]=$chapter;
                    }
                });
            }
            $pageIndex++;
        }
        $chapterArray=array_reverse($chapterArray);

        foreach ($chapterArray as $chap) {
            $chapter= new Chapter();
                $chapter->title=$chap['title'];
                $chapter->url=$chap['url'];
                $chapter->serie()->associate($serie);
                $chapter->save();
        };
    }

    /**
     * adds extra info, is specific to the site
     */
    protected function addExtraInfo($chapterCrawler) {
        $info = $chapterCrawler->filter('body div.flex.flex-col.h-screen.justify-between main div.mx-auto.py-8.grid.max-w-3xl.grid-cols-1.gap-4.sm\\:px-6.lg\\:max-w-screen-2xl.lg\\:grid-flow-col-dense.lg\\:grid-cols-3 div div.focus\\:outline-none.max-w-6xl.bg-white.dark\\:bg-neutral-850.rounded.lg\\:hidden div div dl div');
        $infoSerie = [];
        $index = 1;
        echo "\n\nREPORT".$this->data['url']."\n";
        $statusPresent=false;
        $statusUpdate=false;

        $infoSerie["serieStatus"]='Unknown';

        $info->each(function($node) use (&$infoSerie, $statusPresent,$statusUpdate) {
            if(!$statusPresent){
                if (strpos($node->filter('dt')->text(), "Source Status") !== false) {
                    echo'HIIIIIIII';
                    $infoSerie["serieStatus"]=$node->filter('dd')->text();
                    $statusPresent=true;
                }
            }

            if(!$statusUpdate){
                if (strpos($node->filter('dt')->text(), "Release Status") !== false) {
                    echo 'YOOOOOOO';
                    $infoSerie["serieStatus"]=$node->filter('dd')->text();
                    $statusUpdate=true;
                }
            }
        });

        // if($infoSerie["serieStatus"]==null){
        //     $infoSerie["serieStatus"]="Unknown";
        // }

        // Adding genres to infoSerie array
        $infoSerie['serieGenres'] = "N/A";
        $infoSerie['serieAuthor']='N/A';
        $infoSerie['serieArtists']='N/A';
        $infoSerie['serieCompany']='N/A';
        $infoSerie['serieType']='N/A';

        return $infoSerie;
    }
}
