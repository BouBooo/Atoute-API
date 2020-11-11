<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Company;
use App\Entity\Offer;
use App\Repository\ApplicationRepository;
use App\Repository\OfferRepository;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/applications", name="application_")
 */
class ApplicationController extends BaseController
{
    private AuthService $authService;
    private EntityManagerInterface $manager;
    private OfferRepository $offerRepository;
    private ApplicationRepository $applicationRepository;

    public function __construct(AuthService $authService, EntityManagerInterface $manager)
    {
        $this->authService = $authService;
        $this->manager = $manager;
        $this->offerRepository = $manager->getRepository(Offer::class);
        $this->applicationRepository = $manager->getRepository(Application::class);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = $this->testJson($request);

        if (!array_key_exists('offerId', $data) || !array_key_exists('message', $data)) {
            return $this->respondWithError('invalid_keys');
        }

        if (!$offer = $this->offerRepository->find($data['offerId'])) {
            return $this->respondWithError('offer_not_found');
        }

        $user = $this->authService->getUser();

        if ($user instanceof Company) {
            return $this->respondWithError('company_cant_applied');
        }

        if ($this->offerRepository->hasAlreadyApplied($offer->getId(), $user->getId())) {
            return $this->respondWithError('has_already_applied');
        }

        $application = (new Application())
            ->setOffer($offer)
            ->setCandidate($user)
            ->setMessage($data['message'] ?? null)
        ;

        $this->manager->persist($application);
        $this->manager->flush();

        return $this->respond('application_created');
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        if (!$application = $this->applicationRepository->find($id)) {
            return $this->respondWithError('application_not_found');
        }

        $this->manager->remove($application);
        $this->manager->flush();

        return $this->respond('application_removed');
    }
}