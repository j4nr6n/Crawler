<?php

namespace App\Crawler;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;

interface CrawlerInterface
{
    public function crawl(array $urlParts): DomCrawler;
}
