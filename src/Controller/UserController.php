<?php

namespace App\Controller;

use App\Manager\UserManager;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/user", name="user_")
 */
final class UserController extends BaseController
{
    private EntityManagerInterface $entityManager;
    private AuthService $authService;
    private SerializerInterface $serializer;
    private UserManager $userManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        AuthService $authService,
        SerializerInterface $serializer,
        UserManager $userManager
    ) {
        $this->entityManager = $entityManager;
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
            ['groups' => 'read']
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
}
