<?php

namespace App\Tests\Controller;

use App\Entity\Offer;
use App\Entity\Company;
use App\Entity\Particular;
use App\Tests\ApiTestCase;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

class OfferTest extends ApiTestCase
{
    public function testValidOfferProperties(): void
    {
        ['offer1' => $offer] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);
        
        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertInstanceOf(Company::class, $offer->getOwner());
    }
}