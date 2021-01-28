<?php

namespace App\Tests;

use App\Entity\Offer;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTestCase extends WebTestCase
{
    use FixturesTrait;

    protected const BASE_TOKEN = 'a54w4de4s51f484v5c1qc';
    protected const DIR_FIXTURES = './tests/Fixtures/';

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    public function getBearerToken(string $email)
    {
        return self::$container->get('lexik_jwt_authentication.encoder')->encode(['username' => $email]);
    }

    public function jsonRequest(string $method, string $url, ?array $data = null, ?string $token = null): string
    {
        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Accept' => 'application/json',
            'HTTP_Authorization' => 'Bearer '.$token
        ];

        $this->client->request($method, $url, [], [], $headers, $data ? json_encode($data) : null);

        return $this->client->getResponse()->getContent();
    }

    public function assertJsonEqualsToJson(string $response, string $status, string $message, array $data = []): void
    {
        $json = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];

        $this->assertJsonStringEqualsJsonString($response, json_encode($json));
    }

    public function assertJsonEqualsToJsonJwt(string $response, int $code, string $message): void
    {
        $json = [
            'code' => $code,
            'message' => $message
        ];

        $this->assertJsonStringEqualsJsonString($response, json_encode($json));
    }

    protected function assertHasValidationErrors(object $entity, int $number = 0): void
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($entity);
        $this->assertCount($number, $errors);
        self::tearDown(); // To handle multiple asserts in a row
    }

    protected function createUser(string $email, string $password, bool $isVerified = false): string
    {
        return $this->jsonRequest('POST', '/auth/register', [
            'email' => $email,
            'password' => 'password',
            'role' => 'particular',
            'companyName' => 'The company',
            'is_verified' => $isVerified
        ]);
    }

    protected function createOffer($withErrors = false): array
    {
        return [
            'title' => $withErrors ? '' : "Offer title",
            'description' => 'Offer description',
            'start_at' => null,
            'end_at' => null,
            'city' => 'Bordeaux',
            'postal_code' => '33000',
            'salary' => null,
            'type' => 'Offer type',
            'activity' => 'Offer activity',
            'status' => Offer::DRAFT
        ];
    }

    protected function createApplication($offerId, $resumeId): array
    {
        return [
            'offerId' => $offerId,
            'message' => 'Voici ma candidature',
            'resumeId' => $resumeId,
        ];
    }

    protected function generateRandomEmail(): string
    {
        return 'unit_test@' . md5(microtime()) . '.com';
    }
}