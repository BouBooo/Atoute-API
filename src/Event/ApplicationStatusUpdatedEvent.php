<?php

namespace App\Event;

use App\Entity\Application;
use App\Entity\Particular;
use Symfony\Contracts\EventDispatcher\Event;

final class ApplicationStatusUpdatedEvent extends Event
{
    private Application $application;
    private string $message;

    public function __construct(Application $application, string $message)
    {
        $this->application = $application;
        $this->message = $message;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getApplicationOwner(): Particular
    {
        return $this->application->getCandidate();
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}