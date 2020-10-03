<?php

namespace App\MessageHandler;

use App\Crawler\CrawlerInterface;
use App\Message\Crawl;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CrawlHandler implements MessageHandlerInterface
{
    private CrawlerInterface $crawler;

    public function __construct(CrawlerInterface $crawler)
    {
        $this->crawler = $crawler;
    }

    public function __invoke(Crawl $crawl)
    {
        $urlParts = $crawl->getUrlParts();

        $this->crawler->crawl($urlParts);
    }
}
