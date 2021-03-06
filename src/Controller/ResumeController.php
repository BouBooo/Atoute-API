<?php

namespace App\Controller;

use App\Entity\Resume;
use App\Form\ResumeType;
use App\Security\Voter\ResumeVoter;
use App\Uploader\Uploader;
use App\Service\AuthService;
use App\Repository\ResumeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Resume")
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
        $resume = $this->getAndVerifyResume($id);

        $json = $this->serializer->serialize(
            $resume,
            'json',
            ['groups' => 'resume_read']
        );

        return $this->respond('resume_infos', json_decode($json));
    }

    /**
     * @Route("/{id}", name="update", methods={"PATCH"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $resume = $this->getAndVerifyResume($id);

        if (!$resume instanceof Resume) {
            return $this->respondWithError($resume);
        }

        if (!$resume->isOwner($this->authService->getUser())) {
            return $this->respondWithError('company_can_create_resume');
        }

        $data = $request->request->all();

        $form = $this->formFactory->create(ResumeType::class, $resume, [
            'cv' => $request->files->get('cv')
        ]);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = $this->getFormErrors($form);

            return $this->respondWithError('validation_errors', [
                'errors' => $errors
            ]);
        }

        $this->entityManager->flush();

        return $this->respond('resume_updated');
    }

    /**
     * @Route("", name="all", methods={"GET"})
     */
    public function all(Request $request): JsonResponse
    {
        $limit = (int) $request->query->get('l') !== 0
            ? (int) $request->query->get('l') : null;

        $json = $this->serializer->serialize(
            $this->resumeRepository->getAllPublics($limit),
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

        if (!$resume->isOwner($this->authService->getUser())) {
            return $this->respondWithError('not_resume_owner');
        }

        $this->uploader->remove($resume->getCv());

        $this->entityManager->remove($resume);
        $this->entityManager->flush();

        return $this->respond('resume_removed');
    }

    /**
     * @return Resume|string
     */
    private function getAndVerifyResume(int $id)
    {
        if (!$resume = $this->resumeRepository->find($id)) {
            return 'resume_not_found';
        }

        return $resume;
    }
}
