<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 */
class Company extends User
{
    public const ROLE = 'company';

    /**
     * @Groups({"read"})
     */
    private string $role = self::ROLE;

    /**
     * @ORM\Column(type="string", length=180)
     * @Groups({"offer_read", "read"})
     */
    private string $companyName = '';

    /**
     * @ORM\OneToMany(targetEntity=Offer::class, mappedBy="owner", orphanRemoval=true)
     *
     * @var Collection&iterable<Offer>
     */
    private Collection $offers;

    public function __construct()
    {
        $this->offers = new ArrayCollection();
    }

    public function getRoles(): array
    {
        return ['ROLE_COMPANY'];
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * @return Collection|Offer[]
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): self
    {
        if (!$this->offers->contains($offer)) {
            $this->offers[] = $offer;
            $offer->setOwner($this);
        }

        return $this;
    }
}
