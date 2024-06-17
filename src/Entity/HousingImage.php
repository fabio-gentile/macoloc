<?php

namespace App\Entity;

use App\Repository\HousingImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HousingImageRepository::class)]
class HousingImage
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'], message: 'Le format de l’image doit être {{ choices }}.')]
    private ?string $mimeType = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\NotIdenticalTo(propertyPath: 'filename', message: 'Le nom du fichier original doit être différent du nom du fichier.')]
    private ?string $original_filename = null;

    #[ORM\Column]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $uploadedAt = null;

    #[ORM\ManyToOne(inversedBy: 'housingImages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Housing $housing = null;

    public function __construct()
    {
        $this->uploadedAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->original_filename;
    }

    public function setOriginalFilename(string $original_filename): static
    {
        $this->original_filename = $original_filename;

        return $this;
    }

    public function getUploadedAt(): ?\DateTimeImmutable
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeImmutable $uploadedAt): static
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    public function getHousing(): ?Housing
    {
        return $this->housing;
    }

    public function setHousing(?Housing $housing): static
    {
        $this->housing = $housing;

        return $this;
    }
}
