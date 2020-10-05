# Crawler Thing

**Note:** That's a working title. These are working docs! :laughing: 

# Example Crawler
```php
use App\Crawler\CrawlerInterface;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class MyCrawler implements CrawlerInterface
{
    private CrawlerInterface $decorated;

    public function __construct(CrawlerInterface $decorated) {
        $this->decorated = $decorated;    
    }

    public function crawl(array $urlParts): DomCrawler
    {
        // Pre request kind of things.

        $crawler = $this->decorated->crawl($urlParts);

        // $crawler is a DOM Crawler. Crawl the DOM...

        return $crawler;
    }
}
```

That's pretty much it. ¯\\\_(ツ)_/¯

Because the class implements `CrawlerInterface`, it will automatically
decorate the core crawler.

You can checkout the [`OpenGraphCrawler`](src/Crawler/OpenGraphCrawler.php)
or the [`LinkCrawler`](src/Crawler/LinkCrawler.php) as additional examples.

Run `bin/console crawler:crawl 'https://some-url.tld'` to dispatch
a crawl. Then run `bin/console messenger:consume async -vvv` to
process the crawl. For now, the only "output" from a crawl, is the
dumped results of each crawler. 

I've commented out the bit that causes spidering in
[`LinkCrawler`](src/Crawler/LinkCrawler.php#L56-L58). That's the bit
that causes a crawl to be dispatched for each link. If you uncomment
those lines and restart your consumer, the crawler will quickly run
away from you on its mission to crawl everything it can find.



