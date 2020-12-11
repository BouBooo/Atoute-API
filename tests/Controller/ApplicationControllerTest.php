<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use App\Enum\ApiResponseEnum;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApplicationControllerTest extends ApiTestCase 
{
    public function testParticularAlreadyApplied(): void
    {
        ['offer1' => $offer, 'resume1' => $resume] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $application = $this->createApplication($offer->getId(), $resume->getId());
        $token = $this->getBearerToken($resume->getOwner()->getEmail());

        $response = $this->jsonRequest('POST', '/applications', $application, $token);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertJsonEqualsToJson($response, BaseController::ERROR, ApiResponseEnum::USER_ALREADY_APPLIED);
    }

    public function testParticularCanApply(): void
    {
        ['offer5' => $offer, 'resume1' => $resume] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $application = $this->createApplication($offer->getId(), $resume->getId());
        $token = $this->getBearerToken($resume->getOwner()->getEmail());

        $response = $this->jsonRequest('POST', '/applications', $application, $token);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, ApiResponseEnum::APPLICATION_CREATED);
    }

    public function testParticularCannotApply(): void
    {
        ['particular1' => $particular, 'offer1' => $offer, 'resume1' => $resume] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $application = $this->createApplication($offer->getId(), $resume->getId());
        $token = $this->getBearerToken($particular->getEmail());

        $response = $this->jsonRequest('POST', '/applications', $application, $token);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertJsonEqualsToJson($response, BaseController::ERROR, ApiResponseEnum::RESUME_BAD_OWNER);
    }

    public function testCompanyCannotApply(): void
    {
        ['company1' => $company, 'offer1' => $offer, 'resume1' => $resume] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $application = $this->createApplication($offer->getId(), $resume->getId());
        $token = $this->getBearerToken($company->getEmail());

        $response = $this->jsonRequest('POST', '/applications', $application, $token);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertJsonEqualsToJson($response, BaseController::ERROR, ApiResponseEnum::USER_CANNOT_APPLY);
    }

    public function testDeleteApplication(): void
    {
        ['application1' => $application] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $token = $this->getBearerToken($application->getCandidate()->getEmail());

        $response = $this->jsonRequest('DELETE', '/applications/' . $application->getId(), [], $token);
        
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertJsonEqualsToJson($response, BaseController::SUCCESS, ApiResponseEnum::APPLICATION_REMOVED);
    }

    public function testDeleteNotExistingApplication(): void
    {
        ['application1' => $application] = $this->loadFixtureFiles([
            self::DIR_FIXTURES . 'Entities.yaml',
        ]);

        $token = $this->getBearerToken($application->getCandidate()->getEmail());

        $response = $this->jsonRequest('DELETE', '/applications/60', [], $token);
        
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertJsonEqualsToJson($response, BaseController::ERROR, ApiResponseEnum::APPLICATION_NOT_FOUND);
    }
}