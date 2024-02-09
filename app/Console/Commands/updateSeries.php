<?php

namespace App\Console\Commands;

use App\Models\Scanlator;
use Illuminate\Console\Command;
use App\Services\ScraperService;

class updateSeries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-series';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the chapters in all existing series';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scanlators=Scanlator::all();
        $scraperService=new ScraperService($scanlators);
        $scraperService->updateSeries();
    }
}
