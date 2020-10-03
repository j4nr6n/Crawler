<?php

namespace App\Crawler;

use Symfony\Component\Panther\Client;

abstract class AbstractCrawler implements CrawlerInterface
{
    protected CrawlerInterface $crawler;
    protected Client $client;
    protected array $results;

    public function __construct(CrawlerInterface $crawler)
    {
        $this->crawler = $crawler;

        $this->client = Client::createFirefoxClient();
    }

    public function getResults(): array
    {
        return $this->results;
    }
}
