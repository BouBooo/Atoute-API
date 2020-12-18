<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass=ApplicationRepository::class)
 */
class Application
{
    use TimestampableTrait;

    public const SEND = "send";
    public const ACCEPTED = "accepted";
    public const REFUSED = "refused";

    public static array $applicationStatus = [self::SEND, self::ACCEPTED, self::REFUSED];
    public static array $updatedStatus = [self::ACCEPTED, self::REFUSED];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"application_read", "application_offer_read", "application_user_read"})
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Offer::class, inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"application_read", "application_user_read"})
     */
    private Offer $offer;

    /**
     * @ORM\ManyToOne(targetEntity=Particular::class, inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"application_read", "application_offer_read", "application_user_read"})
     */
    private Particular $candidate;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"application_read", "application_offer_read", "application_user_read"})
     */
    private ?string $message = null;

    /**
     * @ORM\Column(type="string", length=255, options={"default":"self::SEND"})
     * @Groups({"application_read", "application_offer_read", "application_user_read"})
     */
    private string $status = self::SEND;

    /**
     * @ORM\ManyToOne(targetEntity=Resume::class, inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"application_read", "application_offer_read", "application_user_read"})
     */
    private Resume $resume;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isOfferOwner(int $userId): bool
    {
        return $this->offer->getOwner()->getId() === $userId;
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

    public function isOwner(int $userId): bool
    {
        return $this->candidate->getId() === $userId;
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

    public function getResume(): Resume
    {
        return $this->resume;
    }

    public function setResume(Resume $resume): self
    {
        $this->resume = $resume;

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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
