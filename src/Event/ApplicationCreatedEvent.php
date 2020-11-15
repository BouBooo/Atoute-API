<?php

namespace App\Event;

use App\Entity\Application;
use App\Entity\Company;
use App\Entity\Particular;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class ApplicationCreatedEvent extends Event
{
    private Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getOfferOwner(): Company
    {
        return $this->application->getOffer()->getOwner();
    }

}