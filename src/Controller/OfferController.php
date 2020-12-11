<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Form\OfferType;
use App\Security\Voter\OfferVoter;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory
    ) {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->offerRepository = $entityManager->getRepository(Offer::class);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = $this->testJson($request);

        if (!$this->isGranted(OfferVoter::CREATE)) {
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
        $limit = (int) $request->query->get('l') !== 0
            ? (int) $request->query->get('l') : null;

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
        $offer = $this->getAndVerifyOffer($id);

        if (!$offer instanceof Offer) {
            return $this->respondWithError($offer);
        }

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

        if (!$this->isGranted(OfferVoter::EDIT, $offer)) {
            return $this->respondWithError('only_companies_can_create_offer');
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

        if (!$this->isGranted(OfferVoter::EDIT, $offer)) {
            return $this->respondWithError('not_offer_owner');
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

        if (!$this->isGranted(OfferVoter::EDIT, $offer)) {
            return $this->respondWithError('not_offer_owner');
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
    private function getAndVerifyOffer(int $id)
    {
        if (!$offer = $this->offerRepository->find($id)) {
            return 'offer_not_found';
        }

        return $offer;
    }
}