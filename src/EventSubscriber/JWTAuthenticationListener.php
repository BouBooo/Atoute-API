<?php

namespace App\EventSubscriber;

use App\Controller\BaseController;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTAuthenticationListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
            Events::AUTHENTICATION_FAILURE  => 'onAuthenticationFailure'
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();

        $data = array_merge([
            'status' => BaseController::SUCCESS,
            'message' => 'logged_successfully'
        ], $data);

        $event->setData($data);
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $message = $event->getException()->getMessage();
        if (empty($message)) {
            return;
        }

        /** @var JWTAuthenticationFailureResponse $response */
        $response = $event->getResponse();
        $response->setMessage($message);

        $event->setResponse($response);
    }
}