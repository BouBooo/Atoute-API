<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Particular;
use App\Entity\User;
use App\Event\UserCreatedEvent;
use App\Repository\UserRepository;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Authentication")
 * @Route("/auth", name="app_")
 */
final class SecurityController extends BaseController
{
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(
        Request $request,
        TokenGeneratorService $tokenGeneratorService,
        UserPasswordEncoderInterface $encoder,
        EventDispatcherInterface $dispatcher
    ): JsonResponse {
        $data = $this->testJson($request);

        if (!array_key_exists('email', $data) || !array_key_exists('password', $data) || !array_key_exists('role', $data)) {
            return $this->respondWithError('empty_credentials');
        }

        if (!in_array($data['role'], User::$roles, true)) {
            return $this->respondWithError('role_doesnt_exists');
        }

        if ($this->userRepository->isAlreadyExists($data['email'])) {
            return $this->respondWithError('email_already_exists');
        }

        $userClass = Company::ROLE === $data['role'] ? Company::class : Particular::class;

        /** @var User $user */
        $user = $this->serializer->denormalize($data, $userClass);

        $hash = $encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($hash)
            ->setConfirmationToken($tokenGeneratorService->generate());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $dispatcher->dispatch(new UserCreatedEvent($user));

        return $this->respond('registered_successfully');
    }

    /**
     * @Route("/register/check/{id}/{token}", name="check_register")
     */
    public function check(int $id, string $token): JsonResponse
    {
        if (!$userExists = $this->userRepository->find($id)) {
            return $this->respondWithError('user_not_found');
        }

        if (empty($token) || $token !== $userExists->getConfirmationToken()) {
            return $this->respondWithError('invalid_token');
        }

        $userExists->setConfirmationToken(null);
        $userExists->setIsVerified(true);
        $this->entityManager->flush();

        return $this->respond('user_verified');
    }
}
