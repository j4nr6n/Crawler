<?php

namespace App\Crawler;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;

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

    public function crawl(array $urlParts): DomCrawler
    {
        $crawler = $this->decorated->crawl($urlParts);

        $openGraphData = $crawler->filterXPath('//*/meta[starts-with(@property, \'og:\')]');

        $results = [];

        /** @var \DOMElement $element */
        foreach ($openGraphData as $element) {
            $results[$element->getAttribute('property')] = $element->getAttribute('content');
        }

        // TODO: Do something with the data.
        dump($results);

        return $crawler;
    }
}
