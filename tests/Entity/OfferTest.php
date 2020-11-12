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
    /**
     * Build valid entity
     */
    public function buildEntity(): Offer
    {
        ['company1' => $company] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        return (new Offer())
        ->setTitle('My title')
        ->setDescription('Blablala')
        ->setCity('Bordeaux')
        ->setPostalCode('33100')
        ->setOwner($company);
    }

    public function assertHasValidationErrors(Offer $offer, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($offer);
        $this->assertCount($number, $errors);
        self::tearDown(); // To handle multiple asserts in a row
    }

    public function testValidEntity() 
    {
        $this->assertHasValidationErrors($this->buildEntity(), 0);
    }

    public function testInvalidEntity() 
    {
        $this->assertHasValidationErrors($this->buildEntity()->setTitle(''), 1);
    }

    public function testInvalidEntityPostalCode() 
    {
        $this->assertHasValidationErrors($this->buildEntity()->setPostalCode('331005'), 1);
        $this->assertHasValidationErrors($this->buildEntity()->setPostalCode(''), 1);
        $this->assertHasValidationErrors($this->buildEntity()->setPostalCode('Paris'), 1);
        $this->assertHasValidationErrors($this->buildEntity()->setPostalCode('33150'), 0);
    }

    public function testValidOfferProperties(): void
    {
        $offer = $this->buildEntity();
        
        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertInstanceOf(Company::class, $offer->getOwner());
    }
}