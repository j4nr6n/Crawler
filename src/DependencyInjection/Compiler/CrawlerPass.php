<?php

namespace App\DependencyInjection\Compiler;

use App\Crawler\Crawler;
use App\Crawler\CrawlerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CrawlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // First, check that the core crawler is defined
        if (!$container->has(Crawler::class)) {
            return;
        }

        // Get all tagged services
        // Anything implementing `CrawlerInterface` is automatically tagged
        // Anything extending `AbstractCrawler` implements `CrawlerInterface`
        $taggedServices = $container->findTaggedServiceIds('app.crawler');

        // Make all crawlers (except the core crawler) decorate the core crawler
        foreach ($taggedServices as $id => $tags) {
            $definition = $container->getDefinition($id);

            if ($id !== Crawler::class) {
                $definition->setDecoratedService(Crawler::class);
            }
        }

        // Make `CrawlerInterface` an alias to this decorated core crawler.
        $container->addAliases([
            CrawlerInterface::class => Crawler::class,
        ]);
    }
}
