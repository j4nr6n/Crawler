<?php

namespace App\Crawler;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface CrawlerInterface
{
    public function crawl(array $urlParts): ResponseInterface;
}
