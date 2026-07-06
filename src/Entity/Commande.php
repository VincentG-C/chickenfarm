<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidEntityTrait;
use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: 'commandes')]
class Commande
{
    use UuidEntityTrait;

    #[Groups(['api:commande:read'])]
    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[Groups(['api:commande:read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateCommande = null;

    #[Groups(['api:commande:read', 'api:commande:write'])]
    #[Assert\NotNull(message: 'Le montant total est obligatoire.')]
    #[Assert\Positive(message: 'Le montant total doit être supérieur à 0.')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montantTotal = null;

    #[Groups(['api:commande:read', 'api:commande:write'])]
    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[Assert\Choice(choices: ['en_attente', 'confirmee', 'en_preparation', 'expediee', 'livree', 'annulee'], message: 'Le statut sélectionné n\'est pas valide.')]
    #[ORM\Column(length: 50)]
    private ?string $statut = 'en_attente';

    #[Groups(['api:commande:read'])]
    #[Assert\Length(max: 50, maxMessage: 'La référence ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(length: 50, unique: true)]
    private ?string $reference = null;

    #[Groups(['api:commande:write'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    /** @var Collection<int, CommandeDetail> */
    #[Groups(['api:commande:read'])]
    #[ORM\OneToMany(targetEntity: CommandeDetail::class, mappedBy: 'commande', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $details;

    #[Groups(['api:commande:read'])]
    #[ORM\OneToOne(mappedBy: 'commande', targetEntity: Livraison::class, cascade: ['persist', 'remove'])]
    private ?Livraison $livraison = null;

    public function __construct()
    {
        $this->details = new ArrayCollection();
        $this->dateCommande = new \DateTimeImmutable();
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

    public function getDateCommande(): ?\DateTimeImmutable
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeImmutable $dateCommande): static
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;
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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    /** @return Collection<int, CommandeDetail> */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(CommandeDetail $detail): static
    {
        if (!$this->details->contains($detail)) {
            $this->details->add($detail);
            $detail->setCommande($this);
        }

        return $this;
    }

    public function removeDetail(CommandeDetail $detail): static
    {
        $this->details->removeElement($detail);
        return $this;
    }

    public function getLivraison(): ?Livraison
    {
        return $this->livraison;
    }

    public function setLivraison(?Livraison $livraison): static
    {
        if ($livraison !== null && $livraison->getCommande() !== $this) {
            $livraison->setCommande($this);
        }
        $this->livraison = $livraison;
        return $this;
    }
}
