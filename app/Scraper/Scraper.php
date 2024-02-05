<?php

namespace App\Scraper;


abstract class Scraper implements IScraper
{
    protected $url;
    protected $src;
    protected $requestCounter;
    protected $counter=0;

    public function __construct($url,$src) {
        $this->url=$url;
        $this->src=$src;
        $this->requestCounter=0;
    }
    protected static function createChapters($chapterCrawler){

    }

    protected static function addExtraInfo($chapterCrawler){

    }

    public function requestCooldown()
    {

        //echo $this->requestCounter;
        if($this->requestCounter>=10){
            //echo 'going to sleep';
            $this->counter++;
            //echo $this->counter;
            sleep(30);
            $this->requestCounter=0;
        }
        else{
            $this->requestCounter++;
        }
    }
}
