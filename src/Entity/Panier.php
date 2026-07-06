<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidEntityTrait;
use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
#[ORM\Table(name: 'paniers')]
class Panier
{
    use UuidEntityTrait;

    #[Groups(['api:panier:read'])]
    #[ORM\OneToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[Groups(['api:panier:read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['api:panier:read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /** @var Collection<int, PanierProduit> */
    #[Groups(['api:panier:read'])]
    #[ORM\OneToMany(targetEntity: PanierProduit::class, mappedBy: 'panier', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;
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

    /** @return Collection<int, PanierProduit> */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(PanierProduit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->setPanier($this);
        }

        return $this;
    }

    public function removeProduit(PanierProduit $produit): static
    {
        $this->produits->removeElement($produit);
        return $this;
    }

    public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->produits as $item) {
            $total += (float) $item->getProduit()->getPrix() * $item->getQuantite();
        }

        return $total;
    }
}
