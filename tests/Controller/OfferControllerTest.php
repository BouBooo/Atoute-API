<?php

namespace App\Tests\Controller;

use App\Controller\BaseController;
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
        ];

        $this->jsonRequest('POST', '/offers', $offer, $owner->getAccessToken());

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

    public function testGetOffer(): void
    {
        ['offer1' => $offer] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $this->jsonRequest('GET', '/offers/' . $offer->getId(), [], $offer->getOwner()->getAccessToken());

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }
}