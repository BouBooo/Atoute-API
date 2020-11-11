<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected const ACCESS_TOKEN = 'a54w4de4s51f484v5c1qc';
    protected const DIR_FIXTURES = './tests/Fixtures/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function jsonRequest(string $method, string $url, ?array $data = null, ?string $token = null): string
    {
        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Accept' => 'application/json',
            'X-ATOUTE-AUTH-TOKEN' => $token
        ];

        $request = $this->client->request($method, $url, [], [], $headers, $data ? json_encode($data) : null);

        return $this->client->getResponse()->getContent();
    }

    public function assertJsonEqualsToJson(string $response, string $status, string $message, array $data = [])
    {
        $json = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];

        $this->assertJsonStringEqualsJsonString($response, json_encode($json));
    }
}