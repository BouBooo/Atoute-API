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
     * @OA\Parameter(
     *     name="q",
     *     in="query",
     *     description="Contenu de la recherche, un titre, une description..."
     * )
     * @OA\Parameter(
     *     name="l",
     *     in="query",
     *     description="Le nombre d'éléments (par défaut à 10)"
     * )
     * @OA\Response(
     *     response=200,
     *     description="",
     *     @OA\JsonContent(
     *        type="object",
     *        @OA\Property(property="status", type="string"),
     *        @OA\Property(property="message", type="string"),
     *        @OA\Property(property="data", type="object"),
     *     )
     * )
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