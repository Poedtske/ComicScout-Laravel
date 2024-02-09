<?php

namespace App\Console\Commands;

use App\Models\Scanlator;
use Illuminate\Console\Command;
use App\Services\ScraperService;

class updateSerieList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-serie-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds series and updates serieList';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scanlators=Scanlator::all();
        $scraperService=new ScraperService($scanlators);
        $scraperService->scrapeSerie();
    }
}
