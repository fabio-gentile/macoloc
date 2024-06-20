<?php

namespace App\Entity;

use App\Repository\UserAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserAccountRepository::class)]
#[ORM\Table(name: 'user_account')]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cette adresse e-mail.')]
class UserAccount implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner votre adresse e-mail')]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: 'Votre adresse e-mail doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Votre adresse e-mail doit contenir au maximum {{ limit }} caractères'
    )]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 40,
        minMessage: 'Votre prénom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Votre prénom doit contenir au maximum {{ limit }} caractères'
    )]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 40,
        minMessage: 'Votre nom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Votre nom doit contenir au maximum {{ limit }} caractères'
    )]
    private ?string $lastname = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\LessThan(
        value: '-18 years',
        message: 'Vous devez avoir au moins 18 ans pour vous inscrire.'
    )]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $gender = null;

    /**
     * @var Collection<int, Housing>
     */
    #[ORM\OneToMany(targetEntity: Housing::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $housings;

    /**
     * @var Collection<int, Tenant>
     */
    #[ORM\OneToMany(targetEntity: Tenant::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $tenants;

    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'userOne')]
    private Collection $conversationsAsUserOne;

    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'userTwo')]
    private Collection $conversationsAsUserTwo;

    #[ORM\OneToOne(targetEntity: UserImage::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserImage $userImage = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->housings = new ArrayCollection();
        $this->tenants = new ArrayCollection();
        $this->conversationsAsUserOne = new ArrayCollection();
        $this->conversationsAsUserTwo = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }


    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection<int, Housing>
     */
    public function getHousings(): Collection
    {
        return $this->housings;
    }

    public function addHousing(Housing $housing): static
    {
        if (!$this->housings->contains($housing)) {
            $this->housings->add($housing);
            $housing->setUser($this);
        }

        return $this;
    }

    public function removeHousing(Housing $housing): static
    {
        if ($this->housings->removeElement($housing)) {
            // set the owning side to null (unless already changed)
            if ($housing->getUser() === $this) {
                $housing->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tenant>
     */
    public function getTenants(): Collection
    {
        return $this->tenants;
    }

    public function addTenant(Tenant $tenant): static
    {
        if (!$this->tenants->contains($tenant)) {
            $this->tenants->add($tenant);
            $tenant->setUser($this);
        }

        return $this;
    }

    public function removeTenant(Tenant $tenant): static
    {
        if ($this->tenants->removeElement($tenant)) {
            // set the owning side to null (unless already changed)
            if ($tenant->getUser() === $this) {
                $tenant->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversationsOne(): Collection
    {
        return $this->conversationsAsUserTwo;
    }

    public function addConversationOne(Conversation $conversation): static
    {
        if (!$this->conversationsAsUserTwo->contains($conversation)) {
            $this->conversationsAsUserTwo->add($conversation);
            $conversation->setUserOne($this);
        }

        return $this;
    }

    public function removeConversationOne(Conversation $conversation): static
    {
        if ($this->conversationsAsUserOne->removeElement($conversation)) {
            // set the owning side to null (unless already changed)
            if ($conversation->getUserOne() === $this) {
                $conversation->setUserOne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversationsTwo(): Collection
    {
        return $this->conversationsAsUserTwo;
    }

    public function addConversationTwo(Conversation $conversation): static
    {
        if (!$this->conversationsAsUserTwo->contains($conversation)) {
            $this->conversationsAsUserTwo->add($conversation);
            $conversation->setUserTwo($this);
        }

        return $this;
    }

    public function removeConversationTwo(Conversation $conversation): static
    {
        if ($this->conversationsAsUserTwo->removeElement($conversation)) {
            // set the owning side to null (unless already changed)
            if ($conversation->getUserTwo() === $this) {
                $conversation->setUserTwo(null);
            }
        }

        return $this;
    }

    public function getUserImage(): ?UserImage
    {
        return $this->userImage;
    }

    public function setUserImage(?UserImage $userImage): static
    {
        // set the owning side of the relation if necessary
        if (
            $userImage !== null &&
            $userImage->getUser() !== $this
        ) {
            $userImage->setUser($this);
        }

        $this->userImage = $userImage;

        return $this;
    }

//    public function removeUserImage(UserImage $userImage): static
//    {
//        if ($this->userImage->removeElement($housing)) {
//            // set the owning side to null (unless already changed)
//            if ($housing->getUser() === $this) {
//                $housing->setUser(null);
//            }
//        }
//
//        return $this;
//    }

    public function getFullname(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
