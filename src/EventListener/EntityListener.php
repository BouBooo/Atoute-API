<?php

namespace App\EventListener;

use App\Entity\Offer;
use App\Entity\Resume;
use App\Service\AuthService;
use Doctrine\ORM\Event\LifecycleEventArgs;

class EntityListener
{
    private const CREATED_AT_FIELD = 'createdAt';
    private const UPDATED_AT_FIELD = 'updatedAt';

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * When entity has just been created and not insert in db.
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (property_exists($entity, self::CREATED_AT_FIELD)) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->setUpdatedAt(new \DateTime());
        }

        if (($entity instanceof Offer || $entity instanceof Resume) && $user = $this->authService->getUserOrNull()) {
            $entity->setOwner($user);
        }
    }

    /**
     * When entity has just been updated and not insert in db.
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * After the entity has just been persist and insert in db.
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * After the entity has just been persist and insert in db.
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }
}