<?php

namespace App\Tests\Controller;

use App\Controller\BaseController;
use App\Tests\ApiTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityControllerTest extends ApiTestCase
{
    use FixturesTrait;

    public function testParticularRegister(): void
    {
        $response = $this->jsonRequest('POST', '/auth/register', [
            'email' => 'test@test.com',
            'password' => 'password',
            'role' => 'particular',
            'firstName' => 'Particular',
            'lastName' => 'Part'
        ]);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, 'registered_successfully');
    }

    public function testCompanyRegister(): void
    {
        $response = $this->jsonRequest('POST', '/auth/register', [
            'email' => 'test1@test.com',
            'password' => 'password',
            'role' => 'particular',
            'companyName' => 'The company'
        ]);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, 'registered_successfully');
    }

    public function testUserLoginWithBadCredentials(): void
    {
        $response = $this->jsonRequest('POST', '/auth/login', [
            'email' => 'aa@aa.com',
            'password' => 'tess',
        ]);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertJsonEqualsToJson($response, BaseController::ERROR, 'invalid_credentials');
    }

    public function testUserLoginWhenIsNotVerified(): void
    {
        $response = $this->jsonRequest('POST', '/auth/login', [
            'email' => 'test@test.com',
            'password' => 'test',
        ]);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertJsonEqualsToJson($response, BaseController::ERROR, 'user_not_verified');
    }

//    public function testUserLoginWithGoodCredentials(): void
//    {
//        $response = $this->jsonRequest('POST', '/auth/login', [
//            'email' => 'test@test.com',
//            'password' => 'password',
//        ]);
//
//        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
//        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, 'logged_successfully');
//    }
}