<?php

namespace App\Crawler;

use App\Message\Crawl;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Parses anchor tag's href attributes and dispatches crawls for each of them.
 */
final class LinkCrawler implements CrawlerInterface
{
    private CrawlerInterface $decorated;
    private HttpClientInterface $httpClient;
    private MessageBusInterface $messageBus;

    public function __construct(
        CrawlerInterface $decorated,
        HttpClientInterface $httpClient,
        MessageBusInterface $messageBus
    ) {
        $this->decorated = $decorated;
        $this->httpClient = $httpClient;
        $this->messageBus = $messageBus;
    }

    public function crawl(array $urlParts): ResponseInterface
    {
        $response = $this->decorated->crawl($urlParts);
        $crawler = new Crawler($response->getContent());

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

        dump($urls);

        return $response;
    }
}
