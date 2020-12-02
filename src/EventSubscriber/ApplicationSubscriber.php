<?php

namespace App\EventSubscriber;

use App\Entity\Application;
use App\Event\ApplicationCreatedEvent;
use App\Event\ApplicationStatusUpdatedEvent;
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
            ApplicationCreatedEvent::class => 'onParticularApplied',
            ApplicationStatusUpdatedEvent::class => 'onStatusUpdated'
        ];
    }

    public function onParticularApplied(ApplicationCreatedEvent $event): void
    {
        $offerOwner = $event->getOfferOwner();
        $application = $event->getApplication();

        $emailCompany = $this->mailer->buildEmail($offerOwner->getEmail(), 'applications/create.html.twig', [
            'offer' => $application->getOffer(),
            'application' => $application,
            'isParticular' => false
        ]);

        $this->mailer->send($emailCompany);

        $emailPart = $this->mailer->buildEmail($application->getCandidate()->getEmail(), 'applications/create.html.twig', [
            'offer' => $application->getOffer(),
            'application' => $application,
            'isParticular' => true
        ]);

        $this->mailer->send($emailPart);
    }

    public function onStatusUpdated(ApplicationStatusUpdatedEvent $event): void
    {
        $application = $event->getApplication();
        $applicationOwner = $event->getApplicationOwner();
        $message = $event->getMessage();

        $email = $this->mailer->buildEmail($applicationOwner->getEmail(), 'applications/update_status.html.twig', [
            'offer' => $application->getOffer(),
            'companyName' => $application->getOffer()->getOwner()->getCompanyName(),
            'message' => $message,
            'isAccepted' => $application->getStatus() === Application::ACCEPTED
        ]);

        $this->mailer->send($email);
    }
}