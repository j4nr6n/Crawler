<?php

namespace App\Crawler;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Parses Open Graph data
 */
class OpenGraphCrawler extends AbstractCrawler
{
    public function crawl(array $urlParts): DomCrawler
    {
        $crawler = $this->crawler->crawl($urlParts);

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
