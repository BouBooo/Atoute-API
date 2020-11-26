<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 */
class Particular extends User
{
    public const MR = 'Mr';
    public const Mme = "Mme";

    public static array $civilities = [self::MR, self::Mme];

    public const ROLE = 'particular';

    /**
     * @Groups({"read", "resume_read"})
     */
    private string $role = self::ROLE;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "resume_read", "application_read"})
     */
    private string $firstName = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "resume_read", "application_read"})
     */
    private string $lastName = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "resume_read", "application_read"})
     */
    private string $civility = '';

    /**
     * @var Collection&iterable<Resume>
     * @ORM\OneToMany(targetEntity=Resume::class, mappedBy="owner", orphanRemoval=true)
     * @Groups({"read"})
     */
    private Collection $resumes;

    /**
     * @var Collection&iterable<Application>
     * @ORM\OneToMany(targetEntity=Application::class, mappedBy="candidate", orphanRemoval=true)
     * @Groups({"read"})
     */
    private Collection $applications;

    public function __construct()
    {
        $this->resumes = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

    public function getRoles(): array
    {
        return ['ROLE_PARTICULAR'];
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCivility(): string
    {
        return $this->civility;
    }

    public function setCivility(string $civility): self
    {
        $this->civility = $civility;

        return $this;
    }

    public function getResumes(): Collection
    {
        return $this->resumes;
    }

    public function addResume(Resume $resume): self
    {
        if (!$this->resumes->contains($resume)) {
            $this->resumes[] = $resume;
            $resume->setOwner($this);
        }

        return $this;
    }

    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
            $application->setCandidate($this);
        }

        return $this;
    }
}
