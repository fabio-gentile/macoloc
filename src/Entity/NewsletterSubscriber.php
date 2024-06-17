<?php

namespace App\Entity;

use App\Repository\NewsletterSubscriberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Unique;

#[ORM\Entity(repositoryClass: NewsletterSubscriberRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse email est déjà inscrite à la newsletter')]
class NewsletterSubscriber
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $subscribedAt = null;

    #[ORM\Column]
    private ?bool $isVerified = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastSentAt = null;

    /**
     * @var Collection<int, NewsletterReference>
     */
    #[ORM\OneToMany(targetEntity: NewsletterReference::class, mappedBy: 'subscriber', cascade: ['persist'])]
    private Collection $newsletterReferences;

    public function __construct()
    {
        $this->subscribedAt = new \DateTimeImmutable();
        $this->newsletterReferences = new ArrayCollection();
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

    public function getSubscribedAt(): ?\DateTimeImmutable
    {
        return $this->subscribedAt;
    }

    public function setSubscribedAt(\DateTimeImmutable $subscribedAt): static
    {
        $this->subscribedAt = $subscribedAt;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getLastSentAt(): ?\DateTimeInterface
    {
        return $this->lastSentAt;
    }

    public function setLastSentAt(?\DateTimeInterface $lastSentAt): static
    {
        $this->lastSentAt = $lastSentAt;

        return $this;
    }

    /**
     * @return Collection<int, NewsletterReference>
     */
    public function getNewsletterReferences(): Collection
    {
        return $this->newsletterReferences;
    }

    public function addNewsletterReference(NewsletterReference $newsletterReference): static
    {
        if (!$this->newsletterReferences->contains($newsletterReference)) {
            $this->newsletterReferences->add($newsletterReference);
            $newsletterReference->setSubscriber($this);
        }

        return $this;
    }

    public function removeNewsletterReference(NewsletterReference $newsletterReference): static
    {
        if ($this->newsletterReferences->removeElement($newsletterReference)) {
            // set the owning side to null (unless already changed)
            if ($newsletterReference->getSubscriber() === $this) {
                $newsletterReference->setSubscriber(null);
            }
        }

        return $this;
    }
}
