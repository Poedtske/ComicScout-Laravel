<?php
namespace App\Services;

use App\Scraper\AsuraScansScraper;
use App\Scraper\FlameComicsScraper;
use App\Scraper\RizzComicScraper;

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
