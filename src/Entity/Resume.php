<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ResumeRepository;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ResumeRepository::class)
 */
class Resume
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"resume_read", "application_read"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resume_read", "application_read"})
     */
    private string $title = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resume_read", "application_read"})
     */
    private string $contractType = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"resume_read", "application_read"})
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resume_read", "application_read"})
     */
    private string $cv = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resume_read", "application_read"})
     */
    private string $activityArea = '';

    /**
     * @ORM\ManyToOne(targetEntity=Particular::class, inversedBy="resumes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resume_read", "application_read"})
     */
    private Particular $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContractType(): string
    {
        return $this->contractType;
    }

    public function setContractType(string $contractType): self
    {
        $this->contractType = $contractType;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCv(): string
    {
        return $this->cv;
    }

    public function setCv(string $cv): self
    {
        $this->cv = $cv;

        return $this;
    }

    public function getActivityArea(): string
    {
        return $this->activityArea;
    }

    public function setActivityArea(string $activityArea): self
    {
        $this->activityArea = $activityArea;

        return $this;
    }

    public function getOwner(): Particular
    {
        return $this->owner;
    }

    public function setOwner(Particular $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function isOwner(User $user): bool
    {
        return $this->owner->getId() === $user->getId();
    }
}
