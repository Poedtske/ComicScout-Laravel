<?php
namespace App\Scraper;
use App\Scraper\Scraper;
use InvalidArgumentException;

class RizzComicScraper extends Scraper
{
    //name of the source where the Scraper scrapes from
    protected $src="RizzComic";

    //Selectors related to series
    protected $seriesList = '#set_bs div.bs';
    protected $serieUrl = 'div a';
    protected $serieTitle = 'div a div.bigor div.tt';
    protected $serieCover = 'div a div.limit img';

    //Selector related to URLs
    protected $url = "https://rizzcomic.com/series";

    //Selectors related to chapters
    protected $chaptersList = '#chapterlist ul li';
    protected $chapterTitle = 'div div a span.chapternum';
    protected $chapterUrl = 'div div a';

    public function __construct() {
        parent::__construct($this->db);

    }

    /**
     * Updates domain name of Scanlater if there is a need to change it
     */
    public function updateDomain($newDomainName)
    {

    }

    /**
     * starts the process of scraping the series and chapters and adding them to the database
     */
    public function run() {

        $noSeries=false;
        $serieCounter=0;
            self::requestCooldown();
            $pageCrawler = $this->client->request('GET', $this->url);
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









                        echo "localCounter: ".++$serieCounter."\n";




                    });
                }
                catch(InvalidArgumentException){
                    echo "node not found 1".$serieCounter;
                }
            }

    }

    /**
     * adds extra info, is specific to the site
     */
    protected function addExtraInfo($chapterCrawler) {
        $info = $chapterCrawler->filter('div.main-info div.info-right div.tsinfo.bixbox.mobile div');
        $infoSerie = [];
        $index = 1;
        try{
            $info->each(function($node) use (&$infoSerie ) {

                //echo $node->text();
                if (strpos($node->text(), "Type") !== false) {
                    $infoSerie['serieType'] = $node->filter('a')->text();
                }
                elseif (strpos($node->text(), 'Status') !== false) {
                    $infoSerie['serieStatus'] = $node->filter('i')->text();
                }
                elseif (strpos($node->text(), "Author") !== false) {
                    $infoSerie["serieAuthor"]= $node->filter('i')->text();
                }
                elseif (strpos($node->text(), 'Serialization') !== false) {
                    $infoSerie["serieCompany"]= $node->filter('i')->text();
                }
                elseif (strpos($node->text(), 'Artist') !== false) {
                    $infoSerie["serieArtists"]= $node->filter('i')->text();
                }
                });

        }
        catch(InvalidArgumentException){
            echo "info not found";
        }

        // Extracting genres
        $genres = $chapterCrawler->filter('div.main-info div.info-right div.info-desc.bixbox div span a');
        $genresArray = [];
        $genres->each(function($node) use (&$genresArray) {
            $genresArray[] = $node->text();
        });

        // Adding genres to infoSerie array
        $infoSerie['serieGenres'] = $genresArray;

        return $infoSerie;
    }
}
