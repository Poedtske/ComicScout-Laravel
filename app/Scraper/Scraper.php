<?php

namespace App\Scraper;


abstract class Scraper implements IScraper
{
    protected $url=null;
    protected $src="Scraper";
    protected $requestCounter;
    protected $counter=0;

    public function __construct() {
        $this->requestCounter=0;
    }
    protected static function createChapters($chapterCrawler,$serie){

    }

    protected static function addExtraInfo($chapterCrawler){

    }

    public function requestCooldown()
    {

        //echo $this->requestCounter;
        if($this->requestCounter>=10){
            //echo 'going to sleep';
            $this->counter++;
            // echo $this->counter;
            sleep(30);
            $this->requestCounter=0;
        }
        else{
            $this->requestCounter++;
        }
    }
}
