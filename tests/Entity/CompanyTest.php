<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Company;
use App\Tests\ApiTestCase;

class CompanyTest extends ApiTestCase
{
    /**
     * Build valid entity
     */
    public function buildEntity(): Company
    {
        return (new Company())
            ->setCompanyName('coTony')
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
        $this->assertHasValidationErrors($this->buildEntity()->setCompanyName(''), 0);
    }

    public function testValidUserProperties(): void
    {
        $user = $this->buildEntity();

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Company::class, $user);
    }
}