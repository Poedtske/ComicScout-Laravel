<?php
namespace App\Scraper;
use App\Models\Chapter;
use App\Scraper\Scraper;
use InvalidArgumentException;

class DemonComicsScraper extends Scraper
{
    protected $src="DemonComics";
    //filters Constants
    // Constants related to series
    protected $seriesList = '#index section:nth-child(6) div.section-body.boxsizing ul li';
    protected $serieUrl = 'a';
    protected $serieTitle = 'a h4';
    protected $serieCover = 'a figure img';

    // Constants related to URLs
    protected $url = "https://demoncomics.org";

    // Constants related to chapters
    protected $chaptersList = '#chpagedlist ul li';
    protected $chapterTitle = 'a strong';
    protected $chapterUrl = 'a';

    public function __construct() {
        parent::__construct($this->db);

    }
    public function updateDomain($newDomainName)
    {

    }
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
                        $serieUrl = $this->url.$node->filter($this->serieUrl)->attr('href');
                        $serieTitle = $node->filter($this->serieTitle)->text();
                        $serieCover = $node->filter($this->serieCover)->attr('src');
                        $serieSrc = $this->src;

                        echo $serieUrl;
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

    protected function addExtraInfo($chapterCrawler) {
        $info = $chapterCrawler->filter('#mangainfo div div');
        $infoSerie = [];
        $index = 1;

        // $authorInfo=$chapterCrawler->filter('#mangainfo div div.author span');
        // $authorInfo->each(function($node,$i=1) use (&$infoSerie ){
        //     if($i==2){
        //         $infoSerie["serieAuthor"]= $node->text();
        //     }
        //     $i++;
        // });
        // $statusInfo=$chapterCrawler->filter('#mangainfo div div.header-stats span');
        // $statusInfo->each(function($node,$i=1) use (&$infoSerie ){
        //     if($i==3){
        //         $infoSerie["serieStatus"]= $node->filter('strong')->text();
        //     }
        //     $i++;
        // });
        try{
            // Extract author information
    $authorInfo = $chapterCrawler->filter('#mangainfo div div.author span')->eq(1)->text();
    $infoSerie["serieAuthor"] = $authorInfo;

    // Extract status information
    $statusInfo = $chapterCrawler->filter('#mangainfo div div.header-stats span')->eq(2)->filter('strong')->text();
    $infoSerie["serieStatus"] = $statusInfo;
        }
        catch(InvalidArgumentException){
            echo "info not found";
        }

        // Extracting genres
        $genres = $chapterCrawler->filter('#mangainfo div div.categories ul li a');
        $genresArray = [];
        $genres->each(function($node) use (&$genresArray) {
            $genresArray[] = $node->text();
        });



        // Adding genres to infoSerie array
        $infoSerie['serieGenres'] = $genresArray;
        $infoSerie['serieType']='N/A';
        $infoSerie["serieArtists"]='N/A';
        $infoSerie["serieCompany"]='N/A';
        return $infoSerie;
    }

    protected function createChapters($chapterCrawler,$serie) {
        $chapterList = $chapterCrawler->filter($this->chaptersList);
        $chapterArray=[];
        $chapterList->each(function($node) use($serie,&$chapterArray)  {
            $chapterTitle = $node->filter($this->chapterTitle)->text();
            $chapterUrl = $this->url.$node->filter($this->chapterUrl)->attr('href');
            $chapter=['title'=>$chapterTitle,
            'url'=>$chapterUrl,];

            $allowed=self::validateChapter($chapterTitle,$serie);
            if($allowed){
                $chapterArray[]=$chapter;
            }
        });
        $chapterArray=array_reverse($chapterArray);

        foreach ($chapterArray as $chap) {
            $chapter= new Chapter();
                $chapter->title=$chap['title'];
                $chapter->url=$chap['url'];
                $chapter->serie()->associate($serie);
                $chapter->save();
        };
    }
}
