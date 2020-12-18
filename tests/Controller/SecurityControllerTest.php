<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use App\Enum\ApiResponseEnum;
use App\Controller\BaseController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityControllerTest extends ApiTestCase
{
    private const PASSWORD = "password";

    public function testParticularRegister(): void
    {
        $email = $this->generateRandomEmail();
        $response = $this->jsonRequest('POST', '/auth/register', [
            'email' => $email,
            'password' => self::PASSWORD,
            'role' => 'particular',
            'firstName' => 'Particular',
            'lastName' => 'Part'
        ]);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, ApiResponseEnum::USER_REGISTERED);
    }

    public function testCompanyRegister(): void
    {
        $email = $this->generateRandomEmail();
        $response = $this->jsonRequest('POST', '/auth/register', [
            'email' => $email,
            'password' => self::PASSWORD,
            'role' => 'particular',
            'companyName' => 'The company'
        ]);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, ApiResponseEnum::USER_REGISTERED);
    }

    
    public function testUserLoginWithBadCredentials(): void
    {
        $response = $this->jsonRequest('POST', '/auth/login', [
            'username' => 'aa@aa.com',
            'password' => 'tess',
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonEqualsToJsonJwt($response, 401, ApiResponseEnum::INVALID_CREDENTIALS);
    }

    public function testUserLoginWhenIsNotVerified(): void
    {
        $email = $this->generateRandomEmail();

        $this->createUser($email, self::PASSWORD);

        $response = $this->jsonRequest('POST', '/auth/login', [
            'username' => $email,
            'password' => self::PASSWORD,
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonEqualsToJsonJwt($response, 401, ApiResponseEnum::USER_NOT_VERIFIED);
    }

    public function testUserLoginWithGoodCredentials(): void
    {
        $email = $this->generateRandomEmail();

        $registration = $this->createUser($email, self::PASSWORD, true);

        $response = $this->jsonRequest('POST', '/auth/login', [
            'username' => $email,
            'password' => self::PASSWORD,
        ]);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }
}