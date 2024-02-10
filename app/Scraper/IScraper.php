<?php
namespace App\Scraper;
interface IScraper{
    public function run();
    public function serieUpdater();
    public function updateDomain($newDomainName);
    public function requestCooldown();
}
