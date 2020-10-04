<?php

namespace App\Crawler;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * The core crawler.
 *
 * This is where the request for page content actually happens.
 * By the time we get here any pre-flight or setup should be done.
 * From here, a DOMCrawler is passed back up the stack of crawlers
 * so that each one can do their thing with the page content.
 */
final class Crawler implements CrawlerInterface
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
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

        $response = $this->httpClient->request('GET', $url, [
            'timeout' => 5,
        ]);

        try {
            /**
             * Symfony's HTTP client is async. Calling `getContent()` will
             * block until the full response content is available. This is
             * also the point that exceptions are thrown.
             */
            return new DomCrawler($response->getContent());
        } catch (ClientException $exception) {
            // Usually a 404 error, but could be other HTTP status codes.
            // TODO: Handle this in a less messengery way?
            throw new RecoverableMessageHandlingException(
                $exception->getMessage(),
                null,
                $exception
            );
        } catch (TransportException $exception) {
            // SSL Certificate validation error for example.
            // I'm not even going to retry these. Fix your shit!
            throw new UnrecoverableMessageHandlingException(
                $exception->getMessage(),
                null,
                $exception
            );
        }
    }
}
