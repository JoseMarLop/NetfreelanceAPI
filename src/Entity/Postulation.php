<?php

namespace App\Entity;

use App\Repository\PostulationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostulationRepository::class)]
class Postulation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\Column(length: 255)]
    private ?string $date = null;

    #[ORM\ManyToOne(inversedBy: 'postulations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $freelancer = null;

    #[ORM\ManyToOne(inversedBy: 'postulations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?project $project = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getFreelancer(): ?user
    {
        return $this->freelancer;
    }

    public function setFreelancer(?user $freelancer): static
    {
        $this->freelancer = $freelancer;

        return $this;
    }

    public function getProject(): ?project
    {
        return $this->project;
    }

    public function setProject(?project $project): static
    {
        $this->project = $project;

        return $this;
    }
}
