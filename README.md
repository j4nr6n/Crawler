# Crawler Thing

**Note:** That's a working title. These are working docs! :laughing: 

# Example Crawler
```php
use App\Crawler\AbstractCrawler;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class MyCrawler extends AbstractCrawler
{
    public function crawl(array $urlParts): DomCrawler
    {
        // Pre request kind of things.

        $crawler = $this->crawler->crawl($urlParts);

        // $crawler is a DOM Crawler. Crawl the DOM...

        return $crawler;
    }
}
```

That's pretty much it. ¯\\\_(ツ)_/¯

Because the class implements `CrawlerInterface` (via `AbstractCrawler`),
it will automatically decorate the core crawler.

Run `bin/console crawler:crawl 'https://some-url.tld'` to dispatch
a crawl. Then run `bin/console messenger:consume async -vvv` to
process the crawl. For now, the only "output" from a crawl, is the
dumped results of each crawler.



