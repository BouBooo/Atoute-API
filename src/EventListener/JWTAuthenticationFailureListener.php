<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTAuthenticationFailureListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_FAILURE  => 'onAuthenticationFailure'
        ];
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