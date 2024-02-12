<?php
namespace App\Services;

use App\Scraper\RizzComicScraper;
use App\Scraper\AsuraScansScraper;
use App\Scraper\ResetScansScraper;
use App\Scraper\DemonComicsScraper;
use App\Scraper\FlameComicsScraper;
use App\Scraper\ReaperScansScraper;

class ScraperService
{
    protected $scanlators;
    protected $scrapers=[];

    public function __construct($scanlators) {
        $this->scanlators=$scanlators;


        self::selectScraper();
    }

    private function selectScraper(){
        $this->scanlators->each(function($scanlator){
            switch ($scanlator->name) {
                case 'AsuraScans':
                    $this->scrapers[]= new AsuraScansScraper(true);
                    break;
                case 'FlamesComics':
                    $this->scrapers[]= new FlameComicsScraper(true);
                    break;
                case 'RizzComic':
                    $this->scrapers[]=new RizzComicScraper(true);
                    break;
                case 'DemonComics':
                    $this->scrapers[]=new DemonComicsScraper(true);
                    break;
                case 'ReaperScans':
                    $this->scrapers[]=new ReaperScansScraper(true);
                    break;
                case 'ResetScans':
                    $this->scrapers[]=new ResetScansScraper(true);
                    break;
                default:
                    return "Scraper does not exist";
                    break;
            }
        });
    }
    public function scrapeSerie(){
        foreach ($this->scrapers as $scraper) {
            $scraper->run();
        }
    }

    public function updateSeries(){
        foreach ($this->scrapers as $scraper) {
            $scraper->serieUpdater();
        }
    }


}
