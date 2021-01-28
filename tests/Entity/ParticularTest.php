<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Particular;
use App\Tests\ApiTestCase;

class ParticularTest extends ApiTestCase
{
    /**
     * Build valid entity
     */
    public function buildEntity(): Particular
    {
        return (new Particular())
            ->setCivility(Particular::MR)
            ->setFirstName('Tony')
            ->setLastName('Pedrero')
            ->setEmail('user@email.test')
            ->setPassword('SuperPassword')
            ->setIsVerified(false);
    }

    public function testValidEntity(): void
    {
        $this->assertHasValidationErrors($this->buildEntity());
    }

    public function testInvalidEntity(): void
    {
        $this->assertHasValidationErrors($this->buildEntity()->setFirstName(''), 0);
    }

    public function testValidUserProperties(): void
    {
        $user = $this->buildEntity();

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Particular::class, $user);
    }
}