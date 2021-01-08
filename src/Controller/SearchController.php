<?php

namespace App\Controller;

use App\Elasticsearch\Builder\IndexBuilder;
use App\Elasticsearch\Query\OfferQuery;
use Elastica\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Search")
 * @Route("/search", name="search_")
 */
final class SearchController extends BaseController
{
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function search(Request $request, Client $client, OfferQuery $offerQuery): JsonResponse
    {
        $q = $request->query->get('q');
        $limit = $request->query->getInt('l', 10);

        $query = $offerQuery->get($q, $limit);

        $foundOffers = $client->getIndex(IndexBuilder::INDEX_NAME)->search($query);

        $results = [];
        foreach ($foundOffers as $offer) {
            $results[] = $offer->getSource();
        }

        return $this->respond('', $results);
    }
}