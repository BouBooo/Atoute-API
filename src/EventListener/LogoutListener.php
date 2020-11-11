<?php

namespace App\EventListener;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $event): void
    {
        $event->setResponse(
            new JsonResponse([
                'status' => BaseController::SUCCESS,
                'message' => 'logout_successfully',
                'data' => []
            ], JsonResponse::HTTP_OK)
        );
    }
}