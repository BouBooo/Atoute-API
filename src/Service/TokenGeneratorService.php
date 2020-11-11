<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TokenGeneratorService
{
    private const TOKEN_VALIDITY = 'P2D';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generate($length = 10): string
    {
        return sha1(random_bytes($length));
    }

    public function generateAuthToken(User $user, $length = 10): void
    {
        do {
            $accessToken = $this->generate($length);
        } while ($this->accessTokenAlreadyExists($accessToken));

        do {
            $refreshToken = $this->generate($length);
        } while ($this->refreshTokenAlreadyExists($refreshToken));

        $user->setAccessToken($accessToken);
        $user->setRefreshToken($refreshToken);
        $user->setExpirationDate((new \DateTime())->add(new \DateInterval(self::TOKEN_VALIDITY)));
    }

    private function accessTokenAlreadyExists(string $token): bool
    {
        $accessToken = $this->entityManager->getRepository(User::class)->findOneBy([
            'accessToken' => $token,
        ]);

        return null !== $accessToken;
    }

    private function refreshTokenAlreadyExists(string $token): bool
    {
        $refreshToken = $this->entityManager->getRepository(User::class)->findOneBy([
            'refreshToken' => $token,
        ]);

        return null !== $refreshToken;
    }
}