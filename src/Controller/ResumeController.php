<?php

namespace App\Controller;

use App\Entity\Resume;
use App\Form\ResumeType;
use App\Uploader\Uploader;
use App\Service\AuthService;
use App\Repository\ResumeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/resumes", name="resume_")
 */
final class ResumeController extends BaseController
{
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private ResumeRepository $resumeRepository;
    private AuthService $authService;
    private Uploader $uploader;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        AuthService $authService,
        Uploader $uploader
    ) {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->resumeRepository = $entityManager->getRepository(Resume::class);
        $this->authService = $authService;
        $this->uploader = $uploader;
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = $request->request->all();

        if ($this->authService->getUser()->isCompany()) {
            return $this->respondWithError('company_can_create_resume');
        }

        $form = $this->formFactory->create(ResumeType::class, null, [
            'cv' => $request->files->get('cv')
        ]);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = $this->getFormErrors($form);

            return $this->respondWithError('validation_errors', [
                'errors' => $errors
            ]);
        }

        $resume = $form->getData();

        $this->entityManager->persist($resume);
        $this->entityManager->flush();

        return $this->respond('resume_created');
    }

    /**
     * @Route("/{id}", name="index", methods={"GET"})
     */
    public function index(int $id): JsonResponse
    {
        $resume = $this->getAndVerifyResume($id, false);

        $json = $this->serializer->serialize(
            $resume,
            'json',
            ['groups' => 'resume_read']
        );

        return $this->respond('resume_infos', json_decode($json));
    }

    /**
     * @Route("", name="all", methods={"GET"})
     */
    public function all(): JsonResponse
    {
        $json = $this->serializer->serialize(
            $this->resumeRepository->getAllPublics(),
            'json',
            ['groups' => 'resume_read']
        );

        return $this->respond('resumes_infos', json_decode($json));
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $resume = $this->getAndVerifyResume($id);

        if (!$resume instanceof Resume) {
            return $this->respondWithError($resume);
        }

        $this->uploader->remove($resume->getCv());

        $this->entityManager->remove($resume);
        $this->entityManager->flush();

        return $this->respond('resume_removed');
    }

    /**
     * @return Resume|string
     */
    private function getAndVerifyResume(int $id, bool $verifyOwner = true)
    {
        if (!$resume = $this->resumeRepository->find($id)) {
            return 'resume_not_found';
        }

        if ($verifyOwner && !$resume->isOwner($this->authService->getUser())) {
            return 'bad_resume_owner';
        }

        return $resume;
    }
}
