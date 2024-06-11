<?php

namespace App\Entity;

use App\Repository\ChamberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChamberRepository::class)]
class Chamber
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\GreaterThanOrEqual(value: 50, message: "Le prix ne peut pas être inférieur à 50€.")]
    private ?string $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $avaibleAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\GreaterThanOrEqual(value: 50, message: "La caution ne peut pas être inférieure à 50€.")]
    private ?string $caution = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    #[Assert\GreaterThanOrEqual(value: 5, message: "La surface ne peut pas être négative ou inférieure à 5m².")]
    private ?string $surfaceArea = null;

    #[ORM\Column]
    private ?bool $furnished = null;

    #[ORM\ManyToOne(inversedBy: 'chambers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Housing $Housing = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getAvaibleAt(): ?\DateTimeInterface
    {
        return $this->avaibleAt;
    }

    public function setAvaibleAt(\DateTimeInterface $avaibleAt): static
    {
        $this->avaibleAt = $avaibleAt;

        return $this;
    }

    public function getCaution(): ?string
    {
        return $this->caution;
    }

    public function setCaution(string $caution): static
    {
        $this->caution = $caution;

        return $this;
    }

    public function getSurfaceArea(): ?string
    {
        return $this->surfaceArea;
    }

    public function setSurfaceArea(string $surfaceArea): static
    {
        $this->surfaceArea = $surfaceArea;

        return $this;
    }

    public function isFurnished(): ?bool
    {
        return $this->furnished;
    }

    public function setFurnished(bool $furnished): static
    {
        $this->furnished = $furnished;

        return $this;
    }

    public function getHousing(): ?Housing
    {
        return $this->Housing;
    }

    public function setHousing(?Housing $Housing): static
    {
        $this->Housing = $Housing;

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
}
