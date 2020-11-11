<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Particular;
use App\Entity\User;
use App\Event\UserCreatedEvent;
use App\Helper\UserHelper;
use App\Repository\UserRepository;
use App\Service\AuthService;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Serializer\SerializerInterface;

/**
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
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(AuthService $authService): JsonResponse
    {
        $user = $authService->getUser();

        return $this->respond('logged_successfully', [
            'accessToken' => $user->getAccessToken(),
            'refreshToken' => $user->getRefreshToken()
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"POST"})
     */
    public function logout(): void {}

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
            throw new AuthenticationException('empty_credentials');
        }

        if ($this->userRepository->isAlreadyExists($data['email'])) {
            throw new AuthenticationException('email_already_exists');
        }

        $userClass = Company::ROLE === $data['role'] ? Company::class : Particular::class;

        /** @var User $user */
        $user = $this->serializer->denormalize($data, $userClass);

        $hash = $encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($hash)
            ->setConfirmationToken($tokenGeneratorService->generate());

        $tokenGeneratorService->generateAuthToken($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $dispatcher->dispatch(new UserCreatedEvent($user));

        return $this->respond('registered_successfully');
    }

    /**
     * @Route("/register/check", name="check_register", methods={"PATCH"})
     */
    public function check(Request $request): JsonResponse
    {
        $data = $this->testJson($request);

        if (!array_key_exists('token', $data) || !array_key_exists('id', $data)) {
            return $this->respondWithError('credentials_not_provided');
        }

        if (!$userExists = $this->entityManager->getRepository(User::class)->find($data['id'])) {
            return $this->respondWithError('user_not_found');
        }

        $token = $data['token'];

        if (empty($token) || $token !== $userExists->getConfirmationToken()) {
            return $this->respondWithError('invalid_token');
        }

        $userExists->setConfirmationToken(null);
        $userExists->setIsVerified(true);
        $this->entityManager->flush();

        return $this->respond('user_verified');
    }
}
