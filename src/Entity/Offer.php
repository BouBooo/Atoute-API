<?php

namespace App\Entity;

use App\Entity\Traits\ActivityTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\TypeTrait;
use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OfferRepository::class)
 */
class Offer
{
    use TimestampableTrait;
    use TypeTrait;
    use ActivityTrait;

    public const DRAFT = "draft";
    public const PUBLISHED = "published";
    public const CLOSED = "closed";

    public const FILTERS = ['activity', 'type', 'words'];
    public const KEYWORDS = ['php', 'symfony', 'laravel', 'magento', 'javascript', 'reactjs'];

    public static array $offerStatus = [self::DRAFT, self::PUBLISHED, self::CLOSED];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"offer_read", "application_read", "application_user_read"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The title field should not be blank")
     * @Groups({"offer_read", "application_read", "application_user_read"})
     */
    private string $title = '';

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="The description field should not be blank")
     * @Groups({"offer_read", "application_read", "application_user_read"})
     */
    private string $description = '';

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"offer_read", "application_read"})
     */
    private ?\DateTime $startAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"offer_read", "application_read"})
     */
    private ?\DateTime $endAt = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The city field should not be blank")
     * @Groups({"offer_read", "application_read", "application_user_read"})
     */
    private string $city = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The postal_code field should not be blank")
     * @Assert\Regex("/^\d{5}$/")
     * @Groups({"offer_read", "application_read", "application_user_read"})
     */
    private string $postalCode = '';

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"offer_read", "application_read", "application_user_read"})
     */
    private ?int $salary = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"offer_read", "application_read"})
     */
    private string $status = self::DRAFT;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"offer_read", "application_read"})
     */
    private ?\DateTime $publishedAt = null;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="offers")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"offer_read", "application_read"})
     */
    private Company $owner;

    /**
     * @var Collection&iterable<Application>
     * @ORM\OneToMany(targetEntity=Application::class, mappedBy="offer", orphanRemoval=true)
     * @Groups({"offer_read", "application_offer_read"})
     */
    private Collection $applications;

    /**
     * @Groups({"offer_read"})
     */
    private int $applicationsCount = 0;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartAt(): ?\DateTime
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTime $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTime
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTime $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setSalary(?int $salary): self
    {
        $this->salary = $salary;

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

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getOwner(): ?Company
    {
        return $this->owner;
    }

    public function setOwner(Company $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function isOwner(User $user): bool
    {
        return $this->owner->getId() === $user->getId();
    }

    public function getApplicationsToBeProcessed(): Collection
    {
        return $this->applications->filter(static fn (Application $application) => $application->getStatus() === Application::SEND);
    }

    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
            $application->setOffer($this);
        }

        return $this;
    }

    public function getApplicationsCount(): int
    {
        return $this->applicationsCount;
    }

    public function setApplicationsCount(): self
    {
        $this->applicationsCount = $this->applications->filter(fn (Application $application) => $application->getStatus() === Application::SEND)->count();

        return $this;
    }
}
