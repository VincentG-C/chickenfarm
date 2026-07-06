<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidEntityTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'produits')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'produit' => Produit::class,
    'oeuf' => Oeuf::class,
    'viande' => Viande::class,
    'ticket' => Ticket::class,
    'nourriture' => Nourriture::class,
])]
class Produit
{
    use UuidEntityTrait;

    #[Groups(['api:produit:read', 'api:produit:write', 'api:commande:read', 'api:panier:read'])]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[Groups(['api:produit:read', 'api:produit:write'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Groups(['api:produit:read', 'api:produit:write', 'api:commande:read', 'api:panier:read'])]
    #[Assert\NotNull(message: 'Le prix est obligatoire.')]
    #[Assert\Positive(message: 'Le prix doit être supérieur à 0.')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix = null;

    #[Groups(['api:produit:read', 'api:produit:write'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $image = null;

    #[Groups(['api:produit:read', 'api:produit:write'])]
    #[Assert\Length(max: 50, maxMessage: 'L\'unité de prix ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $prixUnite = null;

    #[Groups(['api:produit:read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['api:produit:read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Groups(['api:produit:read'])]
    #[ORM\OneToOne(mappedBy: 'produit', targetEntity: Stock::class, cascade: ['persist', 'remove'])]
    private ?Stock $stock = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
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

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getPrixUnite(): ?string
    {
        return $this->prixUnite;
    }

    public function setPrixUnite(?string $prixUnite): static
    {
        $this->prixUnite = $prixUnite;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): static
    {
        if ($stock !== null && $stock->getProduit() !== $this) {
            $stock->setProduit($this);
        }
        $this->stock = $stock;
        return $this;
    }
}
