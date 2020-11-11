<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function jsonRequest(string $method, string $url, ?array $data = null, ?string $token = null): string
    {
        $this->client->request($method, $url, [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Accept' => 'application/json',
            'X-ATOUTE-AUTH-TOKEN' => $token ?? null
        ], $data ? json_encode($data) : null);

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