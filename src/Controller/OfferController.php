<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Enum\EntityEnum;
use App\Form\OfferType;
use App\Service\AuthService;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/offers", name="offer_")
 */
final class OfferController extends BaseController
{
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private OfferRepository $offerRepository;
    private AuthService $authService;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        AuthService $authService
    ) {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->offerRepository = $entityManager->getRepository(Offer::class);
        $this->authService = $authService;
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = $this->testJson($request);

        if ($this->authService->getUser()->isParticular()) {
            return $this->respondWithError('only_companies_can_create_offer');
        }

        $form = $this->formFactory->create(OfferType::class);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = $this->getFormErrors($form);

            return $this->respondWithError('validation_errors', [
                'errors' => $errors
            ]);
        }

        $offer = $form->getData();

        $this->entityManager->persist($offer);
        $this->entityManager->flush();

        $json = $this->serializer->serialize(
            $offer,
            'json',
            ['groups' => 'offer_read']
        );

        return $this->respond('offer_created', json_decode($json));
    }

    /**
     * @Route("", name="all", methods={"GET"})
     */
    public function all(Request $request): JsonResponse
    {
        $limit = (int) $request->query->get('l', null) !== 0
            ? (int) $request->query->get('l', null) : null;

        $json = $this->serializer->serialize(
            $this->offerRepository->getPublish($limit),
            'json',
            ['groups' => 'offer_read']
        );

        return $this->respond('offers_infos', json_decode($json));
    }

    /**
     * @Route("/{id}", name="index", methods={"GET"})
     */
    public function index(int $id): JsonResponse
    {
        $offer = $this->getAndVerifyOffer($id, false);

        $json = $this->serializer->serialize(
            $offer,
            'json',
            ['groups' => 'offer_read']
        );

        return $this->respond('offer_infos', json_decode($json));
    }

    /**
     * @Route("/{id}", name="update", methods={"PATCH"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $offer = $this->getAndVerifyOffer($id);

        if (!$offer instanceof Offer) {
            return $this->respondWithError($offer);
        }

        $data = $this->testJson($request);

        $form = $this->formFactory->create(OfferType::class, $offer);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = $this->getFormErrors($form);

            return $this->respondWithError('validation_errors', [
                'errors' => $errors
            ]);
        }

        $this->entityManager->flush();

        return $this->respond('offer_updated');
    }

    /**
     * @Route("/{id}", name="remove", methods={"DELETE"})
     */
    public function remove(int $id): JsonResponse
    {
        $offer = $this->getAndVerifyOffer($id);

        if (!$offer instanceof Offer) {
            return $this->respondWithError($offer);
        }

        $this->entityManager->remove($offer);
        $this->entityManager->flush();

        return $this->respond('offer_removed');
    }

    /**
     * @Route("/{id}/applications", name="applications", methods={"GET"})
     */
    public function applications(int $id): JsonResponse
    {
        $offer = $this->getAndVerifyOffer($id);

        if (!$offer instanceof Offer) {
            return $this->respondWithError($offer);
        }

        $applications = [];
        foreach ($offer->getApplicationsToBeProcessed() as $application) {
            $applications[] = json_decode($this->serializer->serialize($application, 'json', ['groups' => 'application_offer_read']));
        }

        return $this->respond('', $applications);
    }

    /**
     * @return Offer|string
     */
    private function getAndVerifyOffer(int $id, bool $verifyOwner = true)
    {
        if (!$offer = $this->offerRepository->find($id)) {
            return 'offer_not_found';
        }

        if ($verifyOwner && !$offer->isOwner($this->authService->getUser())) {
            return 'bad_offer_owner';
        }

        return $offer;
    }
}