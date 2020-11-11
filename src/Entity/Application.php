<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApplicationRepository::class)
 */
class Application
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Offer::class, inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     */
    private Offer $offer;

    /**
     * @ORM\ManyToOne(targetEntity=Particular::class, inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     */
    private Particular $candidate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffer(): Offer
    {
        return $this->offer;
    }

    public function setOffer(Offer $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    public function getCandidate(): Particular
    {
        return $this->candidate;
    }

    public function setCandidate(Particular $candidate): self
    {
        $this->candidate = $candidate;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
