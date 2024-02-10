<?php

use App\Scraper\Scraper2;
use App\Scraper\AsuraScansScraper;
use App\Scraper\FlameComicsScraper;
use App\Scraper\RizzComicScraper;
use Symfony\Component\BrowserKit\HttpBrowser;
//use Illuminate\Support\Facades\App

require 'vendor/autoload.php';

// $s=new FlameComicsScraper(,"FlameComics");
// $s->run();

// $s=new AsuraScansScraper();
// $s->run();

// $client = new HttpBrowser();
// $pageCrawler = $client->request('GET', 'https://asuratoon.com/manga/?page=1');
// echo $pageCrawler->filter('div.listupd div.bs')->html();

$s=new RizzComicScraper(false);
$s->run();
