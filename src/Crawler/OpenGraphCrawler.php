<?php

namespace App\Crawler;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Parses Open Graph data
 */
final class OpenGraphCrawler implements CrawlerInterface
{
    private CrawlerInterface $decorated;

    public function __construct(CrawlerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function crawl(array $urlParts): ResponseInterface
    {
        $response = $this->decorated->crawl($urlParts);
        $crawler = new Crawler($response->getContent());

        $openGraphData = $crawler->filterXPath('//*/meta[starts-with(@property, \'og:\')]');

        $results = [];

        /** @var \DOMElement $element */
        foreach ($openGraphData as $element) {
            $results[$element->getAttribute('property')] = $element->getAttribute('content');
        }

        // TODO: Do something with the data.
        dump($results);

        return $response;
    }
}
