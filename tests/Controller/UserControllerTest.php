<?php

namespace App\Tests\Controller;

use App\Controller\BaseController;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserControllerTest extends ApiTestCase 
{
    public function testGetCompany(): void
    {
        ['company1' => $company] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Company.yaml',
        ]);

        $this->jsonRequest('GET', '/user', [], $company->getAccessToken());

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertEquals($company->getRole(), 'company');
    }

    public function testGetParticular(): void
    {
        ['particular1' => $particular] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Particular.yaml',
        ]);

        $this->jsonRequest('GET', '/user', [], $particular->getAccessToken());

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertEquals($particular->getRole(), 'particular');
    }
}