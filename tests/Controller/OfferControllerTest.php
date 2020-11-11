<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class OfferControllerTest extends ApiTestCase 
{
    use FixturesTrait;

    /*
    public function testCreateOffer()
    {
        $companies = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Company.yaml',
        ]);
        
        $owner = $companies['company1'];

        $response = $this->jsonRequest('POST', '/offers', [], $owner->getAccessToken());
        dd($response);
    }


    public function testGetOffer()
    {
        $data = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $offer = $data['offer1'];

        $response = $this->jsonRequest('GET', '/offers/' . $offer->getId(), [], $offer->getOwner()->getAccessToken());
        dd($response);
    }
    */
}