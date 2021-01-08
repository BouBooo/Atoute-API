<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Offer;
use App\Event\ApplicationCreatedEvent;
use App\Event\ApplicationStatusUpdatedEvent;
use App\Repository\ApplicationRepository;
use App\Repository\OfferRepository;
use App\Repository\ResumeRepository;
use App\Security\Voter\ApplicationVoter;
use OpenApi\Annotations as OA;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Application")
 * @Route("/applications", name="application_")
 */
final class ApplicationController extends BaseController
{
    private AuthService $authService;
    private EntityManagerInterface $manager;
    private SerializerInterface $serializer;
    private EventDispatcherInterface $dispatcher;
    private OfferRepository $offerRepository;
    private ApplicationRepository $applicationRepository;

    public function __construct(
        AuthService $authService,
        EntityManagerInterface $manager,
        SerializerInterface $serializer,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->authService = $authService;
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->dispatcher = $dispatcher;
        $this->offerRepository = $manager->getRepository(Offer::class);
        $this->applicationRepository = $manager->getRepository(Application::class);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request, ResumeRepository $resumeRepository): JsonResponse
    {
        $data = $this->testJson($request);

        if (!array_key_exists('offerId', $data) || !array_key_exists('message', $data) || !array_key_exists('resumeId', $data)) {
            return $this->respondWithError('invalid_keys');
        }

        if (!$offer = $this->offerRepository->find($data['offerId'])) {
            return $this->respondWithError('offer_not_found');
        }

        $user = $this->authService->getUser();

        if (!$this->isGranted(ApplicationVoter::CREATE)) {
            return $this->respondWithError('company_cant_applied');
        }

        if (!$resume = $resumeRepository->find($data['resumeId'])) {
            return $this->respondWithError('resume_not_found');
        }

        if (!$resume->isOwner($user)) {
            return $this->respondWithError('not_your_resume');
        }

        if ($this->offerRepository->hasAlreadyApplied($offer->getId(), $user->getId())) {
            return $this->respondWithError('has_already_applied');
        }

        $application = (new Application())
            ->setOffer($offer)
            ->setCandidate($user)
            ->setResume($resume)
            ->setMessage($data['message'] ?? null)
        ;

        $this->manager->persist($application);
        $this->manager->flush();

        $this->dispatcher->dispatch(new ApplicationCreatedEvent($application));

        return $this->respond('application_created');
    }

    /**
     * @Route("/{id}", name="get", methods={"GET"})
     */
    public function index(int $id): JsonResponse
    {
        if (!$application = $this->applicationRepository->find($id)) {
            return $this->respondWithError('application_not_found');
        }

        $user = $this->authService->getUser();

        if (!$application->isOwner($user->getId()) || !$application->isOfferOwner($user->getId())) {
            return $this->respondWithError("bad_application_access");
        }

        $json = $this->serializer->serialize(
            $application,
            'json',
            ['groups' => 'application_read']
        );

        return $this->respond('application_infos', json_decode($json));
    }

    /**
     * @Route("/{id}", name="update", methods={"PATCH"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        if (!$application = $this->applicationRepository->find($id)) {
            return $this->respondWithError('application_not_found');
        }

        $data = $this->testJson($request);
        $user = $this->authService->getUser();

        if (!array_key_exists('status', $data) || !array_key_exists('message', $data)) {
            return $this->respondWithError('bad_keys');
        }

        if ($user->isCompany() && $application->isOwner($user->getId())) {
            return $this->respondWithError('bad_offer_owner');
        }

        $status = $data['status'];

        if (!in_array($status, Application::$updatedStatus, true)) {
            return $this->respondWithError('not_available_status');
        }

        $application->setStatus($status);
        $this->manager->flush();

        $this->dispatcher->dispatch(new ApplicationStatusUpdatedEvent($application, $data['message']));

        return $this->respond('application_updated');
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        if (!$application = $this->applicationRepository->find($id)) {
            return $this->respondWithError('application_not_found');
        }

        if (!$this->isGranted(ApplicationVoter::EDIT, $application)) {
            return $this->respondWithError('bad_offer_owner');
        }

        if (!$this->isGranted(ApplicationVoter::VIEW, $application)) {
            return $this->respondWithError("bad_application_owner");
        }

        $this->manager->remove($application);
        $this->manager->flush();

        return $this->respond('application_removed');
    }
}