<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\TokenGeneratorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth/token", name="auth_token_")
 */
final class TokenController extends BaseController
{
    /**
     * @Route("/refresh", name="refresh", methods={"POST"})
     */
    public function refresh(Request $request, TokenGeneratorService $tokenGeneratorService, UserRepository $userRepository): JsonResponse
    {
        $data = $this->testJson($request);

        if (!array_key_exists('refresh_token', $data)) {
            return $this->respondWithError('missing_fields');
        }

        $user = $userRepository->findOneBy([
            'refreshToken' => $data['refresh_token']
        ]);

        if (null === $user) {
            return $this->respondWithError('refresh_token_not_found');
        }

        $tokenGeneratorService->generateAuthToken($user);
        $this->getDoctrine()->getManager()->flush();

        return $this->respond('access_token_refreshed', [
            'accessToken' => $user->getAccessToken(),
            'refreshToken' => $user->getRefreshToken()
        ]);
    }
}