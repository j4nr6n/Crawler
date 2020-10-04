<?php

namespace App\Crawler;

use RobotsTxtParser\RobotsTxtParser;
use RobotsTxtParser\RobotsTxtValidator;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * This crawler runs on the way in and blocks requests to URLs
 * that `/robots.txt` suggests we don't crawl.
 */
class RobotsTxtCrawler extends AbstractCrawler
{
    private HttpClientInterface $httpClient;
    public function __construct(CrawlerInterface $crawler, HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;

        parent::__construct($crawler);
    }

    /** @throws \Exception */
    public function crawl(array $urlParts): DomCrawler
    {
        if (!$urlParts['host']) {
            throw new \InvalidArgumentException('"host" key not found in url parts.');
        }

        $url = sprintf(
            '%s://%s/robots.txt',
            $urlParts['scheme'] ?? 'http',
            $urlParts['host']
        );

        $response = $this->httpClient->request('GET', $url, [
            'timeout' => 5,
        ]);

        try {
            // Try to get a `/robots.txt`. If it 404s, we'll catch the exception and continue.
            $robotsTxt = $response->getContent();
        } catch (\Exception $exception) {
            // 404 or some other error retrieving `/robots.txt`.
        }

        if (!empty($robotsTxt)) {
            $robotsTxtParser = new RobotsTxtParser($robotsTxt);
            $robotsTxtValidator = new RobotsTxtValidator($robotsTxtParser->getRules());

            // If the `/robots.txt` file asks us not to crawl, we'll throw an exception
            // to abort the crawl (it'll be retried a few times, but will eventually
            // be forgotten about if `/robots.txt` isn't updated).
            if (!$robotsTxtValidator->isUrlAllow($url)) {
                throw new RecoverableMessageHandlingException('robots.txt says nah!');
            }
        }

        // If all goes well, and we are allowed to crawl the URL. Let's continue!
        return $this->crawler->crawl($urlParts);
    }
}
