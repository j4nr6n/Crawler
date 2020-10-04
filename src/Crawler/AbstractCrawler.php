<?php

namespace App\Crawler;

abstract class AbstractCrawler implements CrawlerInterface
{
    protected CrawlerInterface $crawler;

    public function __construct(CrawlerInterface $crawler)
    {
        $this->crawler = $crawler;
    }
}
