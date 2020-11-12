<?php

namespace App\Tests\Controller;

use App\Entity\Offer;
use App\Entity\Company;
use App\Entity\Particular;
use App\Tests\ApiTestCase;
use App\Entity\Application;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApplicationTest extends ApiTestCase
{
    /**
     * Build valid entity
     */
    public function buildEntity(): Application
    {
        ['offer1' => $offer, 'particular1' => $candidate] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        return (new Application())
            ->setMessage('My message')
            ->setOffer($offer)
            ->setCandidate($candidate);
    }

    public function assertHasValidationErrors(Application $application, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($application);
        $this->assertCount($number, $errors);
        self::tearDown(); // To handle multiple asserts in a row
    }

    public function testValidEntity() 
    {
        $this->assertHasValidationErrors($this->buildEntity(), 0);
    }

    public function testValidApplicationProperties(): void
    {
        $application = $this->buildEntity();
        
        $this->assertInstanceOf(Application::class, $application);
        $this->assertInstanceOf(Particular::class, $application->getCandidate());
        $this->assertInstanceOf(Offer::class, $application->getOffer());
    }
}