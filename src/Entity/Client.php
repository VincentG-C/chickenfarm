<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Client extends User
{
    #[Groups(['api:user:read', 'api:user:write'])]
    #[Assert\Length(max: 50, maxMessage: 'Le téléphone ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $telephone = null;

    #[Groups(['api:user:read', 'api:user:write'])]
    #[Assert\Length(max: 500, maxMessage: 'L\'adresse de livraison ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adresseLivraison = null;

    #[Groups(['api:user:read', 'api:user:write'])]
    #[Assert\Length(max: 500, maxMessage: 'L\'adresse de facturation ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adresseFacturation = null;

    #[Groups(['api:user:read', 'api:user:write'])]
    #[ORM\Column]
    private bool $newsletter = false;

    /** @var Collection<int, Commande> */
    #[Groups(['api:user:read'])]
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'client')]
    private Collection $commandes;

    public function __construct()
    {
        parent::__construct();
        $this->commandes = new ArrayCollection();
        $this->addAssignedRole('ROLE_USER');
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(?string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;

        return $this;
    }

    public function getAdresseFacturation(): ?string
    {
        return $this->adresseFacturation;
    }

    public function setAdresseFacturation(?string $adresseFacturation): static
    {
        $this->adresseFacturation = $adresseFacturation;

        return $this;
    }

    public function isNewsletter(): bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): static
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /** @return Collection<int, Commande> */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setClient($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        $this->commandes->removeElement($commande);

        return $this;
    }
}
