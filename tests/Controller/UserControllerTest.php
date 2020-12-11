<?php

namespace App\Tests\Controller;

use App\Entity\Company;
use App\Entity\Particular;
use App\Tests\ApiTestCase;
use App\Controller\BaseController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserControllerTest extends ApiTestCase 
{
    public function testGetCorrectUser(): void
    {
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->getRandomVerifiedUser();

        $token = $this->getBearerToken($testUser->getEmail());

        $response = $this->jsonRequest('GET', '/user', [], $token);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        if($testUser->isCompany()) {
            $this->assertEquals(Company::ROLE, $testUser->getRole());
        }

        if($testUser->isParticular()) {
             $this->assertEquals(Particular::ROLE, $testUser->getRole()); 
        }       

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJson($response);
    }
}