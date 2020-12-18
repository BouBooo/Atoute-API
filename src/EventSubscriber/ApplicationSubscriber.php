<?php

namespace App\EventSubscriber;

use App\Entity\Application;
use App\Service\MailerService;
use App\Event\ApplicationCreatedEvent;
use App\Event\ApplicationStatusUpdatedEvent;
use App\Queue\Message\ApplicationCreatedMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Queue\Message\ApplicationStatusUpdatedMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ApplicationSubscriber implements EventSubscriberInterface
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
            ApplicationCreatedEvent::class => 'onParticularApplied',
            ApplicationStatusUpdatedEvent::class => 'onStatusUpdated'
        ];
    }

    public function onParticularApplied(ApplicationCreatedEvent $event): void
    {
        $offerOwner = $event->getOfferOwner();
        $application = $event->getApplication();

        $this->busInterface->dispatch(new ApplicationCreatedMessage($offerOwner->getId(), $application->getId()));
    }

    public function onStatusUpdated(ApplicationStatusUpdatedEvent $event): void
    {
        $application = $event->getApplication();
        $applicationOwner = $event->getApplicationOwner();
        $message = $event->getMessage();

        $this->busInterface->dispatch(new ApplicationStatusUpdatedMessage($applicationOwner->getId(), $application->getId(), $message));
    }
}