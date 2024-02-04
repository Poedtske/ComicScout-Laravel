<?php

use App\Scraper\AsuraScansScraper;
use App\Scraper\FlameComicsScraper;
use Symfony\Component\BrowserKit\HttpBrowser;
//use Illuminate\Support\Facades\App

require 'vendor/autoload.php';

// $s=new FlameComicsScraper("https://flamecomics.com/series/?page=","FlameComics");
// $s->run();

$s=new AsuraScansScraper('https://asuratoon.com/manga/?page=','AsuraScans');
$s->run();

// $client = new HttpBrowser();
// $pageCrawler = $client->request('GET', 'https://asuratoon.com/manga/?page=1');
// echo $pageCrawler->html();
