<?php

namespace App\Elasticsearch\Indexer;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use Elastica\Client;
use Elastica\Document;

class OfferIndexer
{
    private Client $client;
    private OfferRepository $offerRepository;

    public function __construct(Client $client, OfferRepository $offerRepository)
    {
        $this->client = $client;
        $this->offerRepository = $offerRepository;
    }

    public function buildDocument(Offer $offer): Document
    {
        return new Document(
            (string) $offer->getId(), // Manually defined ID,
            [
                'id' => $offer->getId(),
                'title' => $offer->getTitle(),
                'description' => $offer->getDescription(),
                'city' => $offer->getCity(),
                'postal_code' => $offer->getPostalCode(),
                'owner' => $offer->getOwner()->getCompanyName(),
                'activity' => $offer->getActivity(),
                'type' => $offer->getType(),
                'status' => $offer->getStatus(),
                'published_at' => $offer->getPublishedAt(),
                // Don't necessary
                'startAt' => $offer->getStartAt(),
                'endAt' => $offer->getEndAt(),
                'salary' => $offer->getSalary()
            ]
        );
    }

    public function indexAllDocuments(string $indexName): void
    {
        $offers = $this->offerRepository->findAll();
        $index = $this->client->getIndex($indexName);

        $documents = [];
        foreach ($offers as $offer) {
            $documents[] = $this->buildDocument($offer);
        }

        $index->addDocuments($documents, ['type' => 'offer']);
        $index->refresh();
    }
}