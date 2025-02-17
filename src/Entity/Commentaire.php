<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{

    const MAX_COMMENT_LENGTH = 255;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Assert\NotNull(message: 'L\'utilisateur ne doit pas être vide.')]
    private ?User $users = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\ManyToOne(targetEntity: Recette::class, inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Assert\NotNull(message: 'La recette ne doit pas être vide.')]
    private ?Recette $recette = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le commentaire ne doit pas être vide.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'Le commentaire doit contenir au moins {{ limit }} caractères.',
        max: self::MAX_COMMENT_LENGTH,
        maxMessage: 'Le commentaire ne doit pas dépasser {{ limit }} caractères.'
    )]

    #[Assert\Regex(
        pattern: "/^[\p{L}0-9.,!?;:'\"(){}\[\]\n\r\- ]{10," . self::MAX_COMMENT_LENGTH . "}$/u",
        message: 'Le commentaire contient des caractères non autorisés.'
    )]
    
    private ?string $commentaire = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $isActive = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    public function __construct()
    {
        $this->isActive = false;
        $this->createAt = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
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

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): static
    {
        $this->recette = $recette;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function isActive(): bool
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
}
