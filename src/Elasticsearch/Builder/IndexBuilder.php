<?php

namespace App\Elasticsearch\Builder;

use Elastica\Client;
use Elastica\Index;
use Symfony\Component\Yaml\Yaml;

class IndexBuilder
{
    private const INDEX_NAME = "site";
    private const PATH = "/../../../config/elasticsearch/elasticsearch_index_offers.yaml";

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create(): Index
    {
        // We name our index "offers"
        $index = $this->client->getIndex(self::INDEX_NAME);

        $settings = Yaml::parse(
            file_get_contents(
                __DIR__ . self::PATH
            )
        );

        $index->create($settings, true);

        return $index;
    }
}