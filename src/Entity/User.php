<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    private ?bool $company = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?float $rating = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    /**
     * @var Collection<int, UserLinks>
     */
    #[ORM\OneToMany(targetEntity: UserLinks::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userLinks;

    /**
     * @var Collection<int, UserAbilities>
     */
    #[ORM\OneToMany(targetEntity: UserAbilities::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userAbilities;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $job = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilepcic = null;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'receiver', orphanRemoval: true)]
    private Collection $reviews;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'sender', orphanRemoval: true)]
    private Collection $reviewsWrote;

    #[ORM\Column(nullable: true)]
    private ?int $phone = null;

    public function __construct()
    {
        $this->userLinks = new ArrayCollection();
        $this->userAbilities = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->reviewsWrote = new ArrayCollection();
    }

    public function getId(): ?int
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isCompany(): ?bool
    {
        return $this->company;
    }

    public function setCompany(?bool $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, UserLinks>
     */
    public function getUserLinks(): Collection
    {
        return $this->userLinks;
    }

    public function addUserLink(UserLinks $userLink): static
    {
        if (!$this->userLinks->contains($userLink)) {
            $this->userLinks->add($userLink);
            $userLink->setUser($this);
        }

        return $this;
    }

    public function removeUserLink(UserLinks $userLink): static
    {
        if ($this->userLinks->removeElement($userLink)) {
            // set the owning side to null (unless already changed)
            if ($userLink->getUser() === $this) {
                $userLink->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserAbilities>
     */
    public function getUserAbilities(): Collection
    {
        return $this->userAbilities;
    }

    public function addUserAbility(UserAbilities $userAbility): static
    {
        if (!$this->userAbilities->contains($userAbility)) {
            $this->userAbilities->add($userAbility);
            $userAbility->setUser($this);
        }

        return $this;
    }

    public function removeUserAbility(UserAbilities $userAbility): static
    {
        if ($this->userAbilities->removeElement($userAbility)) {
            // set the owning side to null (unless already changed)
            if ($userAbility->getUser() === $this) {
                $userAbility->setUser(null);
            }
        }

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getProfilepcic(): ?string
    {
        return $this->profilepcic;
    }

    public function setProfilepcic(?string $profilepcic): static
    {
        $this->profilepcic = $profilepcic;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setReceiver($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getReceiver() === $this) {
                $review->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviewsWrote(): Collection
    {
        return $this->reviewsWrote;
    }

    public function addReviewsWrote(Review $reviewsWrote): static
    {
        if (!$this->reviewsWrote->contains($reviewsWrote)) {
            $this->reviewsWrote->add($reviewsWrote);
            $reviewsWrote->setSender($this);
        }

        return $this;
    }

    public function removeReviewsWrote(Review $reviewsWrote): static
    {
        if ($this->reviewsWrote->removeElement($reviewsWrote)) {
            // set the owning side to null (unless already changed)
            if ($reviewsWrote->getSender() === $this) {
                $reviewsWrote->setSender(null);
            }
        }

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): static
    {
        $this->phone = $phone;

        return $this;
    }
}
