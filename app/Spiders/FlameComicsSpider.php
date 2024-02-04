<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;

class FlameComicsSpider extends BasicSpider
{

    public array $startUrls = [
        "https://flamecomics.com/series/?page=1",
        // 'https://roach-php.dev/docs/spiders'
    ];

    // protected function initialRequests(): array
    // {
    //     $pageNr=1;
    //     $noPage=false;
    //     $urlPagesList=[];
    //     while($noPage){

    //     }
    // }

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $spiderMiddleware = [
        //
    ];

    public array $itemProcessors = [
        //
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;
    /**
     * here we'll fill in the startUrls array with all the
     * pages that the website has
     */
    public function checkPages(Response $response){


    }

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        // $title = $response->filter('h1')->text();
        // $subtitle = $response
        //     ->filter('main > div:nth-child(2) p:first-of-type')
        //     ->text();

        #content > div.wrapper > div.postbody > div.bixbox.seriesearch > div.mrgn > div.listupd > div:nth-child(1) > div > a > div.bigor > div.tt
        $serieList=$response->filter('div.listupd div.bs');
        // foreach ($serieList as $serie) {
        //     $serieLink=$serie->filter('div a')->text();
        //     $serieTitle=$serie->filter('div a div.bigor div.tt')->text();
        //     $serieCover=$serie->filter('div a div.limit img')->text();
        // }

        $serieList->each(function($node){
            $serieLink= $node->filter('div a')->attr('href');
            $serieTitle = $node->filter('div a div.bigor div.tt')->text();
            $serieCover = $node->filter('div a div.limit img')->text();
        })

        //$emptyPageCheck


        yield $this->item([
            'serieLink'=>$serieLink,
            'serieTitle' => $serieTitle,
            'serieCover' => $serieCover,
        ]);
    }
}
