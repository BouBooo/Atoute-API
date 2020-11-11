<?php

namespace App\Entity\Traits;

use App\Enum\EntityEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait TypeTrait
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"offer_read"})
     */
    protected string $type = '';

    public function getType(): string
    {
        return $this->type;
    }

    public function getTypes(): array
    {
        return EntityEnum::$types;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}