<?php

namespace App\Tests\Controller;

use App\Controller\BaseController;
use App\Entity\Offer;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class OfferControllerTest extends ApiTestCase 
{
    public function testCompanyCanCreateOffer(): void
    {
        ['company1' => $owner] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Company.yaml',
        ]);

        $offer = [
            'title' => "Offer title",
            'description' => 'Offer description',
            'start_at' => null,
            'end_at' => null,
            'city' => 'Bordeaux',
            'postal_code' => '33000',
            'salary' => null,
            'type' => 'Offer type',
            'activity' => 'Offer activity',
            'status' => Offer::DRAFT
        ];

        $this->jsonRequest('POST', '/offers', $offer, $owner->getAccessToken());

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

    public function testCreateOfferWithValidationErrors(): void
    {
        ['company1' => $owner] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Company.yaml',
        ]);

        $offer = [
            'title' => "",
            'description' => 'Offer description',
            'city' => 'Bordeaux',
            'postal_code' => '33000',
            'type' => 'Offer type',
            'activity' => "Activity",
            'status' => Offer::DRAFT
        ];

        $this->jsonRequest('POST', '/offers', $offer, $owner->getAccessToken());

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testGetOffer(): void
    {
        ['offer1' => $offer] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $this->jsonRequest('GET', '/offers/' . $offer->getId(), [], $offer->getOwner()->getAccessToken());

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

    public function testDeleteOffer(): void
    {
        ['offer1' => $offer] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $response = $this->jsonRequest('DELETE', '/offers/' . $offer->getId(), [], $offer->getOwner()->getAccessToken());
        
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, 'offer_removed');
    }

    public function testDeleteOfferWithBadOwner(): void
    {
        ['offer1' => $offer] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $badToken = self::BASE_TOKEN . '9';

        $response = $this->jsonRequest('DELETE', '/offers/' . $offer->getId(), [], $badToken);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertJsonEqualsToJson($response, BaseController::ERROR, 'bad_offer_owner');
    }
}