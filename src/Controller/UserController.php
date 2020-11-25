<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Resume;
use App\Manager\UserManager;
use App\Service\AuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/user", name="user_")
 */
final class UserController extends BaseController
{
    private AuthService $authService;
    private SerializerInterface $serializer;
    private UserManager $userManager;

    public function __construct(
        AuthService $authService,
        SerializerInterface $serializer,
        UserManager $userManager
    ) {
        $this->authService = $authService;
        $this->serializer = $serializer;
        $this->userManager = $userManager;
    }

    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $json = $this->serializer->serialize(
            $this->authService->getUser(),
            'json',
            ['groups' => ['read']]
        );

        return $this->respond('user_infos', json_decode($json));
    }

    /**
     * @Route("", name="update", methods={"PATCH"})
     */
    public function update(Request $request): JsonResponse
    {
        $data = $this->testJson($request);
        $user = $this->authService->getUser();

        $this->userManager->update($data, $user);

        return $this->respond('user_updated');
    }

    /**
     * @Route("", name="delete", methods={"DELETE"})
     */
    public function delete(): JsonResponse
    {
        $user = $this->authService->getUser();

        $this->userManager->delete($user);

        return $this->respond('user_removed');
    }

    /**
     * @Route("/resumes", name="resumes", methods={"GET"})
     */
    public function resumes(): JsonResponse
    {
        $user = $this->authService->getUser();

        if ($user->isCompany()) {
            return $this->respondWithError('company_cant_have_resumes');
        }

        $resumes = [];
        /** @var Resume $resume */
        foreach ($user->getResumes() as $resume) {
            $resumes[] = json_decode($this->serializer->serialize($resume, 'json', ['groups' => 'resume_read']));
        }

        return $this->respond('resumes_infos', $resumes);
    }

    /**
     * @Route("/applications", name="applications", methods={"GET"})
     */
    public function applications(): JsonResponse
    {
        $user = $this->authService->getUser();

        if ($user->isCompany()) {
            return $this->respondWithError('company_cant_have_applications');
        }

        $applications = [];
        /** @var Application $application */
        foreach ($user->getApplications() as $application) {
            $applications[] = json_decode($this->serializer->serialize($application, 'json', ['groups' => 'application_read', 'enable_max_depth' => true]));
        }

        return $this->respond('applications_infos', $applications);
    }

    /**
     * @Route("/offers", name="offers", methods={"GET"})
     */
    public function offers(): JsonResponse
    {
        $user = $this->authService->getUser();

        if ($user->isParticular()) {
            return $this->respondWithError('particular_cant_have_offers');
        }

        $offers = [];
        foreach ($user->getOffers() as $offer) {
            $offers[] = json_decode($this->serializer->serialize($offer, 'json', ['groups' => 'offer_read']));
        }

        return $this->respond('offers_infos', $offers);
    }
}
