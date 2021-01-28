<?php 

namespace App\EventSubscriber;

use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

class FailedMessageSubscriber implements EventSubscriberInterface 
{
    private MailerService $mailer;

    public const CONTACT_EMAIL = 'contact@atoute.com';
    public const TWIG_TEMPLATE = 'messages/failure.html.twig';
    
    public function __construct(MailerService $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => 'onMessageFailed'
        ];
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event): void
    {
        $failureEmail = $this->mailer->buildEmail(self::CONTACT_EMAIL, self::TWIG_TEMPLATE, [
            'message' => get_class($event->getEnvelope()->getMessage()),
            'error' => $event->getThrowable()->getMessage(),
            'trace' => $event->getThrowable()->getTraceAsString()
        ]);

        $this->mailer->send($failureEmail);
    }
}