<?php

namespace App\Entity;

use App\Repository\NewsletterReferenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: NewsletterReferenceRepository::class)]
class NewsletterReference
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'newsletterReferences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?NewsletterSubscriber $subscriber = null;

    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private ?Uuid $unsubscribeToken = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sentAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->unsubscribeToken = Uuid::v4();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getSubscriber(): ?NewsletterSubscriber
    {
        return $this->subscriber;
    }

    public function setSubscriber(?NewsletterSubscriber $subscriber): static
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    public function getUnsubscribeToken(): ?Uuid
    {
        return $this->unsubscribeToken;
    }

    public function setUnsubscribeToken(Uuid $unsubscribeToken): static
    {
        $this->unsubscribeToken = $unsubscribeToken;

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

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeInterface $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }
}
