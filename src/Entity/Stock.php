<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidEntityTrait;
use App\Repository\StockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StockRepository::class)]
#[ORM\Table(name: 'stocks')]
class Stock
{
    use UuidEntityTrait;

    #[Groups(['api:produit:read', 'api:stock:read', 'api:stock:write'])]
    #[Assert\NotNull(message: 'La quantité disponible est obligatoire.')]
    #[Assert\PositiveOrZero(message: 'La quantité disponible doit être positive ou nulle.')]
    #[ORM\Column]
    private ?int $quantiteDisponible = 0;

    #[Groups(['api:stock:read', 'api:stock:write'])]
    #[Assert\NotNull(message: 'Le seuil d\'alerte est obligatoire.')]
    #[Assert\PositiveOrZero(message: 'Le seuil d\'alerte doit être positif ou nul.')]
    #[ORM\Column]
    private ?int $seuilAlerte = 10;

    #[Groups(['api:stock:read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateDerniereEntree = null;

    #[Groups(['api:stock:read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateDerniereSortie = null;

    #[ORM\OneToOne(inversedBy: 'stock')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantiteDisponible(): ?int
    {
        return $this->quantiteDisponible;
    }

    public function setQuantiteDisponible(int $quantiteDisponible): static
    {
        $this->quantiteDisponible = $quantiteDisponible;
        return $this;
    }

    public function getSeuilAlerte(): ?int
    {
        return $this->seuilAlerte;
    }

    public function setSeuilAlerte(int $seuilAlerte): static
    {
        $this->seuilAlerte = $seuilAlerte;
        return $this;
    }

    public function getDateDerniereEntree(): ?\DateTimeImmutable
    {
        return $this->dateDerniereEntree;
    }

    public function setDateDerniereEntree(?\DateTimeImmutable $dateDerniereEntree): static
    {
        $this->dateDerniereEntree = $dateDerniereEntree;
        return $this;
    }

    public function getDateDerniereSortie(): ?\DateTimeImmutable
    {
        return $this->dateDerniereSortie;
    }

    public function setDateDerniereSortie(?\DateTimeImmutable $dateDerniereSortie): static
    {
        $this->dateDerniereSortie = $dateDerniereSortie;
        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;
        return $this;
    }

    public function isBelowThreshold(): bool
    {
        return $this->quantiteDisponible !== null && $this->seuilAlerte !== null && $this->quantiteDisponible < $this->seuilAlerte;
    }
}
