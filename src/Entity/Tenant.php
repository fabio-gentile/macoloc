<?php

namespace App\Entity;

use App\Form\Type\ActivityType;
use App\Form\Type\GenderType;
use App\Repository\TenantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TenantRepository::class)]
class Tenant
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: GenderType::GENDER_CHOICES, message: "Veuillez choisir un genre.")]
    private ?string $gender = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ActivityType::ACTIVITY_CHOICES, message: "Veuillez choisir une activité.")]
    private ?string $activity = null;

    #[ORM\Column(length: 255)]
    #[Assert\length(
        min: 2,
        max: 255,
        minMessage: "La ville doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La ville doit contenir au maximum {{ limit }} caractères."
    )]
    private ?string $city = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    #[Assert\GreaterThan(value: 50, message: "Le budget ne peut pas être inférieur à {{ value }}€.")]
    private ?string $budget = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/",
        message: "Longitude invalide."
    )]
    private ?string $longitude = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/",
        message: "Latitude invalide."
    )]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 50,
        max: 500,
        minMessage: "La description doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La description doit contenir au maximum {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: UserAccount::class, inversedBy: 'tenants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserAccount $user = null;

    #[ORM\OneToOne(mappedBy: 'tenant', cascade: ['persist', 'remove'])]
    private ?TenantImage $tenantImage = null;

    #[ORM\Column(nullable: true)]
    private ?int $age = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getActivity(): ?string
    {
        return $this->activity;
    }

    public function setActivity(string $activity): static
    {
        $this->activity = $activity;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getBudget(): ?string
    {
        return $this->budget;
    }

    public function setBudget(string $budget): static
    {
        $this->budget = $budget;

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

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?UserAccount
    {
        return $this->user;
    }

    public function setUser(?UserAccount $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTenantImage(): ?TenantImage
    {
        return $this->tenantImage;
    }

    public function setTenantImage(?TenantImage $tenantImage): static
    {
        // unset the owning side of the relation if necessary
        if ($tenantImage === null && $this->tenantImage !== null) {
            $this->tenantImage->setTenant(null);
        }

        // set the owning side of the relation if necessary
        if ($tenantImage !== null && $tenantImage->getTenant() !== $this) {
            $tenantImage->setTenant($this);
        }

        $this->tenantImage = $tenantImage;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }
}
