<?php

namespace App\Crawler;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler as DomCrawler;

/**
 * The core crawler.
 *
 * This is where the request actually happens.
 * By the time we get here any pre-flight or setup should
 * be done. From here, the Panther crawler is passed back
 * up the stack of crawlers so that each one can do their
 * thing with the page content.
 */
final class Crawler implements CrawlerInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = Client::createFirefoxClient();
    }

    public function crawl(array $urlParts): DomCrawler
    {
        if (!$urlParts['host']) {
            throw new \InvalidArgumentException('"host" key not found in url parts.');
        }

        // Rebuild the URL from it's parts
        $url = sprintf(
            '%s://%s%s%s',
            $urlParts['scheme'] ?? 'http',
            $urlParts['host'],
            $urlParts['path'] ?? '/',
            ($urlParts['fragment'] ?? false) ? '#' . $urlParts['fragment'] : null
        );

        // Make the request and return a DOM crawler
        return $this->client->request('GET', $url);
    }
}
