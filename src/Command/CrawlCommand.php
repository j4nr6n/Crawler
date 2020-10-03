<?php

namespace App\Command;

use App\Message\Crawl;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class CrawlCommand extends Command
{
    protected static $defaultName = 'crawler:crawl';

    protected MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Dispatches a crawl for the given URL')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'The URL to dump OpenGraph data from.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->section(sprintf('Dispatching crawl for "%s"...', $input->getArgument('url')));

        /**
         * TODO: The "URL" passed to Crawl, should be an object.
         *
         * I need to see if there is already a better URL implementation.
         * I'm not sure how much I care for the 'ext-http' one.
         */
        $urlParts = parse_url($input->getArgument('url'));

        $crawl = new Crawl($urlParts);

        $this->messageBus->dispatch($crawl);

        $io->success('Done!');
        $io->note('That just means the crawl was queued.');

        return Command::SUCCESS;
    }
}
