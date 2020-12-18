<?php

namespace App\Tests\Entity;

use App\Entity\Offer;
use App\Entity\Company;
use App\Tests\ApiTestCase;

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
            ->setStatus(Offer::DRAFT)
            ->setOwner($company);
    }

    public function testValidEntity(): void
    {
        $this->assertHasValidationErrors($this->buildEntity());
    }

    public function testInvalidEntity(): void
    {
        $this->assertHasValidationErrors($this->buildEntity()->setTitle(''), 1);
    }

    public function testInvalidEntityPostalCode(): void
    {
        $this->assertHasValidationErrors($this->buildEntity()->setPostalCode('331005'), 1);
        $this->assertHasValidationErrors($this->buildEntity()->setPostalCode(''), 1);
        $this->assertHasValidationErrors($this->buildEntity()->setPostalCode('Paris'), 1);
        $this->assertHasValidationErrors($this->buildEntity()->setPostalCode('33150'));
    }

    public function testValidOfferProperties(): void
    {
        $offer = $this->buildEntity();
        
        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertInstanceOf(Company::class, $offer->getOwner());
    }
}