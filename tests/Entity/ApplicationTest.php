<?php

namespace App\Tests\Entity;

use App\Entity\Offer;
use App\Entity\Particular;
use App\Entity\Resume;
use App\Tests\ApiTestCase;
use App\Entity\Application;

class ApplicationTest extends ApiTestCase
{
    /**
     * Build valid entity
     */
    public function buildEntity(): Application
    {
        ['offer1' => $offer, 'particular1' => $candidate, 'resume1' => $resume] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        return (new Application())
            ->setMessage('My message')
            ->setOffer($offer)
            ->setCandidate($candidate)
            ->setResume($resume);
    }

    public function testValidEntity(): void
    {
        $this->assertHasValidationErrors($this->buildEntity());
    }

    public function testValidApplicationProperties(): void
    {
        $application = $this->buildEntity();
        
        $this->assertInstanceOf(Application::class, $application);
        $this->assertInstanceOf(Particular::class, $application->getCandidate());
        $this->assertInstanceOf(Offer::class, $application->getOffer());
        $this->assertInstanceOf(Resume::class, $application->getResume());
    }
}