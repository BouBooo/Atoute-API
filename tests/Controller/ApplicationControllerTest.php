<?php

namespace App\Tests\Controller;

use App\Controller\BaseController;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApplicationControllerTest extends ApiTestCase 
{
    public function testParticularCantApply(): void
    {
        ['particular1' => $particular, 'offer1' => $offer, 'resume1' => $resume] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $application = [
            'offerId' => $offer->getId(),
            'message' => 'Voici ma candidature',
            'resumeId' => $resume->getId(),
        ];

        $response = $this->jsonRequest('POST', '/applications', $application, $particular->getAccessToken());

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertJsonEqualsToJson($response, BaseController::ERROR, 'not_your_resume');
    }

    public function testCompanyCanApply(): void
    {
        ['company1' => $company, 'offer1' => $offer, 'resume1' => $resume] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $application = [
            'offerId' => $offer->getId(),
            'message' => 'Voici ma candidature',
            'resumeId' => $resume->getId()
        ];

        $response = $this->jsonRequest('POST', '/applications', $application, $company->getAccessToken());

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertJsonEqualsToJson($response, BaseController::ERROR, 'company_cant_applied');
    }

    public function testDeleteApplication(): void
    {
        ['application1' => $application] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $response = $this->jsonRequest('DELETE', '/applications/' . $application->getId(), [], $application->getCandidate()->getAccessToken());
        
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, 'application_removed');
    }

    public function testDeleteNotExistingApplication(): void
    {
        ['application1' => $application] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $response = $this->jsonRequest('DELETE', '/applications/60', [], $application->getCandidate()->getAccessToken());
        
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertJsonEqualsToJson($response, BaseController::ERROR, 'application_not_found');
    }
}