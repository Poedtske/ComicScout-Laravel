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
        $title = $response->filter('h1')->text();

#content > div.wrapper > div.postbody > div.bixbox.seriesearch > div.mrgn > div.listupd > div:nth-child(1) > div > a > div.bigor > div.tt
        $serieList=$response->filter('div.listupd div.bs')->text();
        $serie=$response->filter('div.listupd div.bs div a')->text();
        $serieTitle=$response->filter('div.listupd div.bs div a div.bigor div.tt')->text();
        $serieCover=$response->filter('div.listupd div.bs div a div.limit img')->text();
        $emptyPageCheck
        $subtitle = $response
            ->filter('main > div:nth-child(2) p:first-of-type')
            ->text();

        yield $this->item([
            'title' => $title,
            'subtitle' => $subtitle,
        ]);
    }
}
