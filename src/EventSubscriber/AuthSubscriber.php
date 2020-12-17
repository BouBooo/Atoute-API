<?php

namespace App\EventSubscriber;

use App\Service\MailerService;
use App\Event\UserCreatedEvent;
use App\Event\ResetPasswordEvent;
use App\Queue\Message\UserCreatedMessage;
use App\Queue\Message\ResetPasswordMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthSubscriber implements EventSubscriberInterface
{
    private MailerService $mailer;
    private MessageBusInterface $busInterface;

    public function __construct(MailerService $mailer, MessageBusInterface $busInterface)
    {
        $this->mailer = $mailer;
        $this->busInterface = $busInterface;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::class => 'onRegister',
            ResetPasswordEvent::class => 'onResetPassword'
        ];
    }

    public function onRegister(UserCreatedEvent $event): void
    {
        $user = $event->getUser();

        $this->busInterface->dispatch(New UserCreatedMessage($user->getId()));
    }

    public function onResetPassword(ResetPasswordEvent $event): void
    {
        $user = $event->getUser();

        $this->busInterface->dispatch(New ResetPasswordMessage($user->getId()));
    }
}