<?php

namespace App\Crawler;

use App\Message\Crawl;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Parses anchor tag's href attributes and dispatches crawls for each of them.
 */
class LinkCrawler extends AbstractCrawler
{
    private HttpClientInterface $httpClient;
    private MessageBusInterface $messageBus;

    public function __construct(
        CrawlerInterface $crawler,
        HttpClientInterface $httpClient,
        MessageBusInterface $messageBus
    ) {
        $this->httpClient = $httpClient;
        $this->messageBus = $messageBus;

        parent::__construct($crawler);
    }

    public function crawl(array $urlParts): DomCrawler
    {
        $crawler = $this->crawler->crawl($urlParts);

        $links = $crawler->filterXPath('//*/a[not(@href=\'#\')]');

        $urls = [];

        /** @var \DOMElement $link */
        foreach ($links as $link) {
            $newUrlParts = parse_url($link->getAttribute('href'));

            if (empty($newUrlParts['host'])) {
                // I need to work out the logic for handling relative paths.
                // We'll have to skip them for now.

                continue;
            }

            $urls[] = $newUrlParts;
        }

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
