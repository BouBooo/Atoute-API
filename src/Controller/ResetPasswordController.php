<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\ResetPasswordEvent;
use App\Repository\UserRepository;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/reset-password", name="reset_")
 */
final class ResetPasswordController extends BaseController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        /* @var UserRepository userRepository */
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    /**
     * @Route("", name="index", methods={"POST"})
     */
    public function reset(
        Request $request,
        TokenGeneratorService $tokenGeneratorService,
        EventDispatcherInterface $dispatcher
    ): JsonResponse {
        $data = $this->testJson($request);

        if (!array_key_exists('email', $data)) {
            return $this->respondWithError('email_not_provided');
        }

        $user = $this->userRepository->findOneBy([
            'email' => $data['email']
        ]);

        if (null === $user) {
            return $this->respondWithError('user_not_found');
        }

        $user->setResetPasswordToken($tokenGeneratorService->generate());
        $this->entityManager->flush();

        $dispatcher->dispatch(new ResetPasswordEvent($user));

        return $this->respond("email_send");
    }

    /**
     * @Route("/check", name="check", methods={"PATCH"})
     */
    public function check(Request $request, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        $data = $this->testJson($request);

        if (!array_key_exists('token', $data) || !array_key_exists('id', $data) || !array_key_exists('password', $data)) {
            return $this->respondWithError('credentials_not_provided');
        }

        if (!$userExists = $this->userRepository->find($data['id'])) {
            return $this->respondWithError('user_not_found');
        }

        $token = $data['token'];

        if (empty($token) || $token !== $userExists->getResetPasswordToken()) {
            return $this->respondWithError('invalid_token');
        }

        $userExists->setResetPasswordToken(null);
        $userExists->setPassword($passwordEncoder->encodePassword($userExists, $data['password']));

        $this->entityManager->flush();

        return $this->respond('password_changed');
    }
}