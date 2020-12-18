<?php

namespace App\Tests\Controller;

use App\Entity\Offer;
use App\Tests\ApiTestCase;
use App\Enum\ApiResponseEnum;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

class OfferControllerTest extends ApiTestCase 
{
    public function testCompanyCanCreateOffer(): void
    {
        ['company1' => $owner] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Company.yaml',
        ]);

        $offer = $this->createOffer();

        $token = $this->getBearerToken($owner->getEmail());

        $this->jsonRequest('POST', '/offers', $offer, $token);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

    
    public function testCreateOfferWithValidationErrors(): void
    {
        ['company1' => $owner] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Company.yaml',
        ]);

        $offer = $this->createOffer(true);

        $token = $this->getBearerToken($owner->getEmail());

        $this->jsonRequest('POST', '/offers', $offer, $token);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testGetOffer(): void
    {
        ['offer1' => $offer] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $token = $this->getBearerToken($offer->getOwner()->getEmail());

        $this->jsonRequest('GET', '/offers/' . $offer->getId(), [], $token);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

    public function testDeleteOffer(): void
    {
        ['offer1' => $offer] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $token = $this->getBearerToken($offer->getOwner()->getEmail());

        $response = $this->jsonRequest('DELETE', '/offers/' . $offer->getId(), [], $token);
        
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, ApiResponseEnum::OFFER_DELETE);
    }

    public function testDeleteOfferWithBadOwner(): void
    {
        ['offer1' => $offer, 'offer5' => $offer5] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $badToken = $this->getBearerToken($offer5->getOwner()->getEmail());

        $response = $this->jsonRequest('DELETE', '/offers/' . $offer->getId(), [], $badToken);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertJsonEqualsToJson($response, BaseController::ERROR, ApiResponseEnum::OFFER_BAD_OWNER);
    }
}