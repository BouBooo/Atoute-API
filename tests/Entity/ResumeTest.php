<?php

namespace App\Tests\Entity;

use App\Entity\Resume;
use App\Tests\ApiTestCase;

class ResumeTest extends ApiTestCase
{
    /**
     * Build valid entity
     */
    public function buildEntity(): Resume
    {
        return (new Resume())
            ->setTitle('Recherche Stage Cuisinier')
            ->setContractType('Stage')
            ->setDescription('Recherche de stage')
            ->setCv('null')
            ->setActivityArea('Restauration');

    }

    public function testValidEntity(): void
    {
        $this->assertHasValidationErrors($this->buildEntity());
    }

    public function testInvalidEntity(): void
    {
        $this->assertHasValidationErrors($this->buildEntity()->setTitle(''), 0);
    }

    public function testValidUserProperties(): void
    {
        $resume = $this->buildEntity();

        $this->assertInstanceOf(Resume::class, $resume);
    }
}