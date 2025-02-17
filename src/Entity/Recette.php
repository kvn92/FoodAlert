<?php

namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: RecetteRepository::class)]
#[ORM\Table(name: "recette", uniqueConstraints: [
    new ORM\UniqueConstraint(name: "unique_recette_utilisateur", columns: ["titre", "users_id"])
])]

class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'recettes')]
    private ?User $users = null;

    #[ORM\Column(type: Types::TEXT,length: 100)]
    #[NotBlank(message: 'Le titre ne doit pas être vide.')]
    #[Assert\Length(
        min: 3,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.',
        max: 100,
        maxMessage: 'Le titre ne doit pas dépasser {{ limit }} caractères.'
    )]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT,length: 255)]
    private ?string $photo = null;

    #[ORM\Column(type: Types::TEXT,)]
    #[NotBlank(message: 'La préparation ne doit pas être vide.')]
    #[Assert\Length(
        min: 3,
        minMessage: 'La préparation doit contenir au moins {{ limit }} caractères.',
        max:400,
        maxMessage: 'La préparation ne doit pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}0-9.,!?'\- ]{3,100}$/u",
        message: 'Le titre ne peut contenir que des lettres, chiffres, espaces et ponctuations autorisées (. , ! ? \' -).'
    )]
    private ?string $preparation = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private ?bool $isActive = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'recette')]
    private Collection $commentaires;

    public function __construct()
    {
        $this->isActive = false;
        $this->createAt = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): static
    {
        $this->users = $users;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = trim(strtolower($titre));

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = trim(strtolower($photo));

        return $this;
    }

    public function getPreparation(): ?string
    {
        return $this->preparation;
    }

    public function setPreparation(string $preparation): static
    {
        $this->preparation = $preparation;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setRecette($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getRecette() === $this) {
                $commentaire->setRecette(null);
            }
        }

        return $this;
    }
}
