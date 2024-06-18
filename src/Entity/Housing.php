<?php

namespace App\Entity;

use App\Form\Type\CommodityType;
use App\Form\Type\NumberOfRoomsType;
use App\Repository\HousingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Validator\Constraints as Assert;
use App\Form\Type\HousingTypeType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: HousingRepository::class)]
class Housing
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\GreaterThanOrEqual(value: 50, message: "Le prix ne peut pas être inférieur à {{ value }}€.")]
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: HousingTypeType::HOUSING_TYPE_CHOICES, message: "Type de logement invalide.")]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $numberOfRooms = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    #[Assert\GreaterThanOrEqual(value: 5, message: "La surface ne peut pas être négative ou inférieure à {{ value }}m².")]
    private ?string $surfaceArea = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $avaibleAt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 50,
        max: 2000,
        minMessage: "La description doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La description doit contenir au maximum {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
    private ?array $commodity = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Choice(choices: ['Animaux', 'Fumeurs'], multiple: true, message: "Autres informations invalides.")]
    private ?array $other = null;

    #[ORM\ManyToOne(targetEntity: UserAccount::class, inversedBy: 'housings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserAccount $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/",
        message: "Latitude invalide."
    )]
    private ?string $latitude = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/",
        message: "Longitude invalide."
    )]
    private ?string $longitude = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/^\d{5}$/",
        message: "Code postal invalide."
    )]
    private ?string $postal_code = null;

    #[ORM\Column(length: 255)]
    #[Assert\length(
        min: 2,
        max: 255,
        minMessage: "La ville doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La ville doit contenir au maximum {{ limit }} caractères."
    )]
    private ?string $city = null;

    /**
     * @var Collection<int, Chamber>
     */
    #[ORM\OneToMany(targetEntity: Chamber::class, mappedBy: 'Housing', cascade: ['persist'] ,orphanRemoval: true)]
    private Collection $chambers;

    /**
     * @var Collection<int, HousingImage>
     */
    #[ORM\OneToMany(targetEntity: HousingImage::class, mappedBy: 'housing', cascade: ['persist'])]
    private Collection $housingImages;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le titre doit contenir au maximum {{ limit }} caractères."
    )]
    private ?string $title = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->chambers = new ArrayCollection();
        $this->housingImages = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getNumberOfRooms(): ?int
    {
        return $this->numberOfRooms;
    }

    public function setNumberOfRooms(int $numberOfRooms): static
    {
        $this->numberOfRooms = $numberOfRooms;

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

    public function getAvaibleAt(): ?\DateTimeInterface
    {
        return $this->avaibleAt;
    }

    public function setAvaibleAt(\DateTimeInterface $avaibleAt): static
    {
        $this->avaibleAt = $avaibleAt;

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

    public function getCommodity(): ?array
    {
        return $this->commodity;
    }

    public function setCommodity(?array $commodity): static
    {
        $this->commodity = $commodity;

        return $this;
    }

    public function getOther(): ?array
    {
        return $this->other;
    }

    public function setOther(?array $other): static
    {
        $this->other = $other;

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

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

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

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): static
    {
        $this->postal_code = $postal_code;

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

    /**
     * @return Collection<int, Chamber>
     */
    public function getChambers(): Collection
    {
        return $this->chambers;
    }

    public function addChamber(Chamber $chamber): static
    {
        if (!$this->chambers->contains($chamber)) {
            $this->chambers->add($chamber);
            $chamber->setHousing($this);
        }

        return $this;
    }

    public function removeChamber(Chamber $chamber): static
    {
        if ($this->chambers->removeElement($chamber)) {
            // set the owning side to null (unless already changed)
            if ($chamber->getHousing() === $this) {
                $chamber->setHousing(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HousingImage>
     */
    public function getHousingImages(): Collection
    {
        return $this->housingImages;
    }

    public function addHousingImage(HousingImage $housingImage): static
    {
        if (!$this->housingImages->contains($housingImage)) {
            $this->housingImages->add($housingImage);
            $housingImage->setHousing($this);
        }

        return $this;
    }

    public function removeHousingImage(HousingImage $housingImage): static
    {
        if ($this->housingImages->removeElement($housingImage)) {
            // set the owning side to null (unless already changed)
            if ($housingImage->getHousing() === $this) {
                $housingImage->setHousing(null);
            }
        }

        return $this;
    }

    /**
     * Update the avaibleAt field with the earliest date of all chambers.
     * @return $this
     */
    public function updateAvaibleAt(): static
    {
        $earliestDate = null;

        foreach ($this->getChambers() as $chamber) {
            if ($earliestDate === null || $chamber->getAvaibleAt() < $earliestDate) {
                $earliestDate = $chamber->getAvaibleAt();
            }
        }

        $this->setAvaibleAt($earliestDate);
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Update the price field with the cheapest price of all chambers.
     * @return $this
     */
    public function updatePrice(): static
    {
        $cheapestPrice = null;

        foreach ($this->getChambers() as $chamber) {
            if ($cheapestPrice === null || $chamber->getPrice() < $cheapestPrice) {
                $cheapestPrice = $chamber->getPrice();
            }
        }

        $this->setPrice($cheapestPrice);
        return $this;
    }
}
