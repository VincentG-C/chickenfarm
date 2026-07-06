<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidEntityTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'naissances')]
class Naissance
{
    use UuidEntityTrait;

    #[ORM\ManyToOne(inversedBy: 'naissances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Incubation $incubation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Enclos $enclos = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[Assert\NotNull(message: 'Le nombre de poussins est obligatoire.')]
    #[Assert\Positive(message: 'Le nombre de poussins doit être positif.')]
    #[ORM\Column]
    private ?int $nbPoussins = null;

    #[ORM\Column]
    private int $nbMorts = 0;

    public function __construct()
    {
        $this->date = new \DateTimeImmutable();
    }

    public function getIncubation(): ?Incubation
    {
        return $this->incubation;
    }

    public function setIncubation(?Incubation $incubation): static
    {
        $this->incubation = $incubation;
        return $this;
    }

    public function getEnclos(): ?Enclos
    {
        return $this->enclos;
    }

    public function setEnclos(?Enclos $enclos): static
    {
        $this->enclos = $enclos;
        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getNbPoussins(): ?int
    {
        return $this->nbPoussins;
    }

    public function setNbPoussins(int $nbPoussins): static
    {
        $this->nbPoussins = $nbPoussins;
        return $this;
    }

    public function getNbMorts(): int
    {
        return $this->nbMorts;
    }

    public function setNbMorts(int $nbMorts): static
    {
        $this->nbMorts = $nbMorts;
        return $this;
    }
}
