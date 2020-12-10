<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"particular"="Particular", "company"="Company"})
 */
abstract class User implements UserInterface, \Serializable
{
    use TimestampableTrait;

    public static array $roles = [Particular::ROLE, Company::ROLE];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"offer_read", "resume_read", "application_read"})
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"read", "resume_read", "application_read"})
     */
    protected string $email = '';

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"private"})
     */
    protected string $password = '';

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"private"})
     */
    protected ?string $resetPasswordToken = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"private"})
     */
    private ?string $confirmationToken = null;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     * @Groups({"private"})
     */
    private bool $isVerified = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    abstract public function getRoles(): array;

    public function isCompany(): bool
    {
        return $this instanceof Company;
    }

    public function isParticular(): bool
    {
        return $this instanceof Particular;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): void
    {
        $this->resetPasswordToken = $resetPasswordToken;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->email,
            $this->password
        ] = unserialize($serialized, [$this, Company::class, Particular::class]);
    }
}
