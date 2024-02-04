<?php
namespace App\Scraper;
interface IScraper{
    public function run();
    public function serieUpdater();
    public function chapterUpdater();
    public function updateDomain($newDomainName);
    public function requestCooldown();
}
