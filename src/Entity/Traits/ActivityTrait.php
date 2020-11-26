<?php

namespace App\Entity\Traits;

use App\Enum\EntityEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait ActivityTrait
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"offer_read", "application_read", "read"})
     */
    protected string $activity = '';

    public function getActivity(): string
    {
        return $this->activity;
    }

    public function getActivities(): array
    {
        return EntityEnum::$activities;
    }

    public function setActivity(string $activity): self
    {
        $this->activity = $activity;

        return $this;
    }
}