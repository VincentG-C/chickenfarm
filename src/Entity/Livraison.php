<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidEntityTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'livraisons')]
class Livraison
{
    use UuidEntityTrait;

    #[Groups(['api:commande:read'])]
    #[ORM\OneToOne(inversedBy: 'livraison')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[Groups(['api:commande:read', 'api:livraison:write'])]
    #[Assert\NotBlank(message: 'L\'adresse de livraison est obligatoire.')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $adresseLivraison = null;

    #[Groups(['api:commande:read', 'api:livraison:write'])]
    #[Assert\NotNull(message: 'La date de livraison prévue est obligatoire.')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateLivraisonPrevue = null;

    #[Groups(['api:commande:read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateLivraisonReelle = null;

    #[Groups(['api:commande:read', 'api:livraison:write'])]
    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[Assert\Choice(choices: ['en_preparation', 'expediee', 'en_cours', 'livree', 'annulee'], message: 'Le statut sélectionné n\'est pas valide.')]
    #[ORM\Column(length: 50)]
    private ?string $statut = 'en_preparation';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;
        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    public function getDateLivraisonPrevue(): ?\DateTimeImmutable
    {
        return $this->dateLivraisonPrevue;
    }

    public function setDateLivraisonPrevue(\DateTimeImmutable $dateLivraisonPrevue): static
    {
        $this->dateLivraisonPrevue = $dateLivraisonPrevue;
        return $this;
    }

    public function getDateLivraisonReelle(): ?\DateTimeImmutable
    {
        return $this->dateLivraisonReelle;
    }

    public function setDateLivraisonReelle(?\DateTimeImmutable $dateLivraisonReelle): static
    {
        $this->dateLivraisonReelle = $dateLivraisonReelle;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }
}
