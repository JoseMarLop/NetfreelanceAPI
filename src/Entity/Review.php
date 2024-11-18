<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $receiver = null;

    #[ORM\ManyToOne(inversedBy: 'reviewsWrote')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $sender = null;

    #[ORM\Column]
    private ?int $rating = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getReceiver(): ?user
    {
        return $this->receiver;
    }

    public function setReceiver(?user $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getSender(): ?user
    {
        return $this->sender;
    }

    public function setSender(?user $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }
}
