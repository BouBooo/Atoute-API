<?php

namespace App\Security;

use App\Controller\BaseController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiAuthenticator extends AbstractGuardAuthenticator
{
    private string $headerAuthToken;
    private EntityManagerInterface $entityManager;

    public function __construct(string $headerAuthToken, EntityManagerInterface $entityManager)
    {
        $this->headerAuthToken = $headerAuthToken;
        $this->entityManager = $entityManager;
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has($this->headerAuthToken);
    }

    public function getCredentials(Request $request): array
    {
        return [
            'accessToken' => $request->headers->get($this->headerAuthToken)
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        if (!$credentials['accessToken']) {
            throw new AuthenticationException('token_was_empty');
        }

        /** @var User|null $apiToken */
        $apiToken = $this->entityManager->getRepository(User::class)->findOneBy([
            'accessToken' => $credentials['accessToken'],
        ]);

        if (null === $apiToken) {
            throw new AuthenticationException('token_not_found');
        }

        $now = new \DateTime();
        if (!$apiToken->tokenIsValid($now)) {
            throw new AuthenticationException('token_has_expired');
        }

        return $apiToken;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'status' => BaseController::ERROR,
            'message' => $exception->getMessage(),
            'data' => []
        ], JsonResponse::HTTP_OK);
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse([
            'status' => BaseController::ERROR,
            'message' => 'access_token_invalid',
            'data' => []
        ], JsonResponse::HTTP_OK);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}