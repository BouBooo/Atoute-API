<?php

namespace App\Security;

use App\Controller\BaseController;
use App\Entity\User;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class WebAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    private const LOGIN_ROUTE = 'app_login';

    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;
    private UserPasswordEncoderInterface $passwordEncoder;
    private AuthService $authService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        UserPasswordEncoderInterface $passwordEncoder,
        AuthService $authService
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->passwordEncoder = $passwordEncoder;
        $this->authService = $authService;
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod(Request::METHOD_POST);
    }

    public function getCredentials(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->authService->getUserOrNull();

        if (null !== $user && $user->getEmail() !== $data['email']) {
            $this->authService->logout($request);
        }

        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        if (!$credentials['email'] || !$credentials['password']) {
            throw new AuthenticationException('empty_credentials');
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $credentials['email']
        ]);

        if (null === $user) {
            throw new CustomUserMessageAuthenticationException('invalid_credentials');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    /**
     * @return JsonResponse|RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $errorMessage = $exception->getMessage(); // @TODO: Externalize this in dedicated function
        if (strpos($errorMessage, 'checkCredentials')) {
            $errorMessage = 'invalid_credentials';
        }

        return new JsonResponse([
            'status' => BaseController::ERROR,
            'message' => $errorMessage,
            'data' => []
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @return JsonResponse|RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([
            'status' => BaseController::ERROR,
            'message' => $authException->getMessage(),
            'data' => []
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
