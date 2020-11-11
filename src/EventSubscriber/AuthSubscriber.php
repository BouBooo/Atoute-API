<?php

namespace App\EventSubscriber;

use App\Event\ResetPasswordEvent;
use App\Event\UserCreatedEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthSubscriber implements EventSubscriberInterface
{
    private MailerService $mailer;

    public function __construct(MailerService $mailer)
    {
        $this->mailer = $mailer;
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

        $email = $this->mailer->buildEmail($user->getEmail(), 'emails/confirm_account.html.twig', [
            'id' => $user->getId(),
            'token' => $user->getConfirmationToken()
        ]);

        $this->mailer->send($email);
    }

    public function onResetPassword(ResetPasswordEvent $event): void
    {
        $user = $event->getUser();

        $email = $this->mailer->buildEmail($user->getEmail(), 'emails/reset_password.html.twig', [
            'id' => $user->getId(),
            'token' => $user->getResetPasswordToken()
        ]);

        $this->mailer->send($email);
    }
}