<?php

namespace App\Command;

use App\Elasticsearch\Builder\IndexBuilder;
use App\Elasticsearch\Indexer\OfferIndexer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ElasticReindexCommand extends Command
{
    protected static $defaultName = "elastic:reindex";

    private IndexBuilder $indexBuilder;
    private OfferIndexer $offerIndexer;

    public function __construct(IndexBuilder $indexBuilder, OfferIndexer $offerIndexer)
    {
        $this->indexBuilder = $indexBuilder;
        $this->offerIndexer = $offerIndexer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Rebuild the Index and populate it.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $index = $this->indexBuilder->create();
        $io->success('Index created !');

        $this->offerIndexer->indexAllDocuments($index->getName());
        $io->success('Index populated and ready !');

        return Command::SUCCESS;
    }
}
