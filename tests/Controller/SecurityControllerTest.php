<?php

namespace App\Tests\Controller;

use App\Controller\BaseController;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityControllerTest extends ApiTestCase
{
    private const PASSWORD = "password";

    public function testParticularRegister(): void
    {
        $response = $this->jsonRequest('POST', '/auth/register', [
            'email' => 'test@test.com',
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
        $response = $this->jsonRequest('POST', '/auth/register', [
            'email' => 'test1@test.com',
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

    public function testUserLoginWithGoodCredentials(): void
    {
        ['particular1' => $user] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Particular.yaml'
        ]);

        $response = $this->jsonRequest('POST', '/auth/login', [
            'email' => $user->getEmail(),
            'password' => self::PASSWORD,
        ]);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, 'logged_successfully', [
            'accessToken' => $user->getAccessToken(),
            'refreshToken' => $user->getRefreshToken()
        ]);
    }
}