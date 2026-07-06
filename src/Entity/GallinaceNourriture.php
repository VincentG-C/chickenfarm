<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidEntityTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'gallinaces_nourritures')]
class GallinaceNourriture
{
    use UuidEntityTrait;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Gallinace $gallinace = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Nourriture $nourriture = null;

    #[Assert\NotNull(message: 'La quantité par jour est obligatoire.')]
    #[Assert\Positive(message: 'La quantité par jour doit être positive.')]
    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $quantiteParJour = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateFin = null;

    public function getGallinace(): ?Gallinace
    {
        return $this->gallinace;
    }

    public function setGallinace(?Gallinace $gallinace): static
    {
        $this->gallinace = $gallinace;
        return $this;
    }

    public function getNourriture(): ?Nourriture
    {
        return $this->nourriture;
    }

    public function setNourriture(?Nourriture $nourriture): static
    {
        $this->nourriture = $nourriture;
        return $this;
    }

    public function getQuantiteParJour(): ?string
    {
        return $this->quantiteParJour;
    }

    public function setQuantiteParJour(string $quantiteParJour): static
    {
        $this->quantiteParJour = $quantiteParJour;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeImmutable $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeImmutable $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }
}
