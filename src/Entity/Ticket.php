<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Ticket extends Produit
{
    #[Assert\NotNull(message: 'La date de visite est obligatoire.')]
    #[ORM\Column(type: 'date_immutable')]
    private ?\DateTimeImmutable $dateVisite = null;

    #[Assert\NotNull(message: 'Le nombre de places est obligatoire.')]
    #[Assert\Positive(message: 'Le nombre de places doit être positif.')]
    #[ORM\Column]
    private ?int $nbPlaces = null;

    #[ORM\Column]
    private int $nbPlacesRestantes = 0;

    #[Assert\NotNull(message: 'La durée de visite est obligatoire.')]
    #[Assert\Positive(message: 'La durée de visite doit être positive.')]
    #[ORM\Column]
    private ?int $dureeVisite = 60; // en minutes

    public function getDateVisite(): ?\DateTimeImmutable
    {
        return $this->dateVisite;
    }

    public function setDateVisite(\DateTimeImmutable $dateVisite): static
    {
        $this->dateVisite = $dateVisite;
        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nbPlaces;
    }

    public function setNbPlaces(int $nbPlaces): static
    {
        $this->nbPlaces = $nbPlaces;
        return $this;
    }

    public function getNbPlacesRestantes(): int
    {
        return $this->nbPlacesRestantes;
    }

    public function setNbPlacesRestantes(int $nbPlacesRestantes): static
    {
        $this->nbPlacesRestantes = $nbPlacesRestantes;
        return $this;
    }

    public function getDureeVisite(): ?int
    {
        return $this->dureeVisite;
    }

    public function setDureeVisite(int $dureeVisite): static
    {
        $this->dureeVisite = $dureeVisite;
        return $this;
    }
}
