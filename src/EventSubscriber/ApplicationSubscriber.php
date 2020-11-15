<?php

namespace App\EventSubscriber;

use App\Event\ApplicationCreatedEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ApplicationSubscriber implements EventSubscriberInterface
{
    private MailerService $mailer;

    public function __construct(MailerService $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ApplicationCreatedEvent::class => 'onParticularApplied'
        ];
    }

    public function onParticularApplied(ApplicationCreatedEvent $event): void
    {
        $offerOwner = $event->getOfferOwner();
        $application = $event->getApplication();

        $email = $this->mailer->buildEmail($offerOwner->getEmail(), 'applications/create.html.twig', [
            'offer' => $application->getOffer(),
            'application' => $application
        ]);

        $this->mailer->send($email);
    }
}