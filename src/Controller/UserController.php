<?php

namespace App\Controller;

use DateTime;
use App\Entity\Offer;
use App\Entity\Resume;
use App\Entity\Application;
use App\Manager\UserManager;
use App\Service\AuthService;
use App\Service\CacheService;
use OpenApi\Annotations as OA;
use App\Repository\OfferRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @OA\Tag(name="User")
 * @Route("/user", name="user_")
 */
final class UserController extends BaseController
{
    private AuthService $authService;
    private SerializerInterface $serializer;
    private UserManager $userManager;
    private OfferRepository $offerRepository;
    private CacheService $cacheService;

    public function __construct(
        AuthService $authService,
        SerializerInterface $serializer,
        UserManager $userManager,
        OfferRepository $offerRepository,
        CacheService $cacheService
    ) {
        $this->authService = $authService;
        $this->serializer = $serializer;
        $this->userManager = $userManager;
        $this->offerRepository = $offerRepository;
        $this->cacheService = $cacheService;
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
            $applications[] = json_decode($this->serializer->serialize($application, 'json', ['groups' => 'application_user_read']));
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

    /**
     * @Route("/offers/related", name="offers_related", methods={"GET"})
     */
    public function relatedOffers(CacheInterface $cache): JsonResponse
    {
        $user = $this->authService->getUser();

        if ($user->isCompany()) {
            return $this->respondWithError('company_cant_have_related_offers');
        }

        $offers = [];
        foreach($user->getResumes() as $resume) { 
            $cacheKey = 'user_'.$user->getId().'_resume_'.$resume->getId().'_related_offers';
            $relatedOffers = $this->cacheService->loadRelatedOffers($cacheKey, $resume);     
            $offers[] = json_decode($this->serializer->serialize($relatedOffers->get(), 'json', ['groups' => 'offer_read']));
        }

        return $this->respond('related_offers', $offers);
    }
}
