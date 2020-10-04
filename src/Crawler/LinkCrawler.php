<?php

namespace App\Crawler;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Panther\DomCrawler\Crawler as DomCrawler;

class LinkCrawler extends AbstractCrawler
{
    private MessageBusInterface $messageBus;

    public function __construct(CrawlerInterface $crawler, MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;

        parent::__construct($crawler);
    }

    public function crawl(array $urlParts): DomCrawler
    {
        $crawler = $this->crawler->crawl($urlParts);

        $links = $crawler->filterXPath('//*/a');

        $urls = [];

        /** @var RemoteWebElement $link */
        foreach ($links as $link) {
            $urlParts = parse_url($link->getAttribute('href'));

            if (empty($urlParts['host'])) {
                // Sorry, gotta have it for now.
                continue;
            }

            $urls[] = $urlParts;
        }

        dump($urls);

        /**
         * And this makes it a spider. It crawls the web.
         * I've commented this out to avoid the system going absolutely
         * ape shit while I'm playing around.
         */
        // foreach ($urls as $urlParts) {
        //     $this->messageBus->dispatch(new Crawl($urlParts));
        // }

        return $crawler;
    }
}
