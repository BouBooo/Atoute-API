<?php

namespace App\Elasticsearch\Query;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MultiMatch;

class OfferQuery
{
    public function get(string $query, int $limit): Query
    {
        $match = new MultiMatch();
        $match->setQuery($query);
        $match->setFields([
            "title^4", // Add weight priority => ^4
            "description",
            "city",
            "owner",
            "postal_code"
        ]);

        $filter = new Query\Term();
        $filter->setTerm('status', 'published'); // Get offers published

        $bool = new BoolQuery();
        $bool->addMust($match);
        $bool->addFilter($filter);

        return (new Query($bool))
            ->setSize($limit);
    }
}