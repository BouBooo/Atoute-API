<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use App\Controller\BaseController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityControllerTest extends ApiTestCase
{
    private const PASSWORD = "password";
    private const JWT_INVALID_CREDENTIALS = "Bad credentials.";
    private const AUTH_NOT_VERIFIED = "user_not_verified";

    public function testParticularRegister(): void
    {
        $email = 'unit_test@' . md5(microtime()) . '.com';
        $response = $this->jsonRequest('POST', '/auth/register', [
            'email' => $email,
            'password' => self::PASSWORD,
            'role' => 'particular',
            'firstName' => 'Particular',
            'lastName' => 'Part'
        ]);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, 'registered_successfully');
    }

    public function testCompanyRegister(): void
    {
        $email = 'unit_test@' . md5(microtime()) . '.com';
        $response = $this->jsonRequest('POST', '/auth/register', [
            'email' => $email,
            'password' => self::PASSWORD,
            'role' => 'particular',
            'companyName' => 'The company'
        ]);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, 'registered_successfully');
    }

    
    public function testUserLoginWithBadCredentials(): void
    {
        $response = $this->jsonRequest('POST', '/auth/login', [
            'username' => 'aa@aa.com',
            'password' => 'tess',
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonEqualsToJsonJwt($response, 401, self::JWT_INVALID_CREDENTIALS);
    }

    public function testUserLoginWhenIsNotVerified(): void
    {
        $email = 'unit_test@' . md5(microtime()) . '.com';

        $this->createUser($email, self::PASSWORD);

        $response = $this->jsonRequest('POST', '/auth/login', [
            'username' => $email,
            'password' => self::PASSWORD,
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonEqualsToJsonJwt($response, 401, self::AUTH_NOT_VERIFIED);
    }

    public function testUserLoginWithGoodCredentials(): void
    {
        $email = 'unit_test@' . md5(microtime()) . '.com';

        $registration = $this->createUser($email, self::PASSWORD, true);

        $response = $this->jsonRequest('POST', '/auth/login', [
            'username' => $email,
            'password' => self::PASSWORD,
        ]);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }
}