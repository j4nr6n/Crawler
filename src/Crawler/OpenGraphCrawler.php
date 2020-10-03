<?php

namespace App\Crawler;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Symfony\Component\Panther\DomCrawler\Crawler as DomCrawler;

class OpenGraphCrawler extends AbstractCrawler
{
    public function crawl(array $urlParts): DomCrawler
    {
        $crawler = $this->crawler->crawl($urlParts);

        $openGraphData = $crawler->filterXPath('//*/meta[starts-with(@property, \'og:\')]');

        $results = [];

        /** @var RemoteWebElement $element */
        foreach ($openGraphData as $element) {
            $results[$element->getAttribute('property')] = $element->getAttribute('content');
        }

        dump($results);

        return $crawler;
    }
}
