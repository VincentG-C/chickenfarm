<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Oeuf extends Produit
{
    #[Assert\NotBlank(message: 'Le calibre est obligatoire.')]
    #[Assert\Choice(choices: ['petit', 'moyen', 'gros', 'extra_gros'], message: 'Le calibre sélectionné n\'est pas valide.')]
    #[ORM\Column(length: 50)]
    private ?string $calibre = null;

    #[ORM\Column]
    private bool $estFeconde = false;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $datePonte = null;

    #[ORM\ManyToOne(inversedBy: 'oeufs')]
    private ?Poule $poule = null;

    public function getCalibre(): ?string
    {
        return $this->calibre;
    }

    public function setCalibre(string $calibre): static
    {
        $this->calibre = $calibre;
        return $this;
    }

    public function isEstFeconde(): bool
    {
        return $this->estFeconde;
    }

    public function setEstFeconde(bool $estFeconde): static
    {
        $this->estFeconde = $estFeconde;
        return $this;
    }

    public function getDatePonte(): ?\DateTimeImmutable
    {
        return $this->datePonte;
    }

    public function setDatePonte(?\DateTimeImmutable $datePonte): static
    {
        $this->datePonte = $datePonte;
        return $this;
    }

    public function getPoule(): ?Poule
    {
        return $this->poule;
    }

    public function setPoule(?Poule $poule): static
    {
        $this->poule = $poule;
        return $this;
    }
}
