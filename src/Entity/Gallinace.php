<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidEntityTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'gallinaces')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'gallinace' => Gallinace::class,
    'poule' => Poule::class,
    'coq' => Coq::class,
    'poussin' => Poussin::class,
])]
class Gallinace
{
    use UuidEntityTrait;

    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 100, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[Assert\NotNull(message: 'L\'âge est obligatoire.')]
    #[Assert\PositiveOrZero(message: 'L\'âge doit être positif ou nul.')]
    #[ORM\Column]
    private ?int $age = 0;

    #[Assert\NotNull(message: 'Le poids est obligatoire.')]
    #[Assert\Positive(message: 'Le poids doit être positif.')]
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $poids = null;

    #[Assert\NotBlank(message: 'L\'état de santé est obligatoire.')]
    #[Assert\Choice(choices: ['excellent', 'bon', 'moyen', 'malade', 'blesse'], message: 'L\'état de santé sélectionné n\'est pas valide.')]
    #[ORM\Column(length: 50)]
    private ?string $sante = 'bon';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateEntree = null;

    #[ORM\ManyToOne(inversedBy: 'gallinaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Enclos $enclos = null;

    public function __construct()
    {
        $this->dateEntree = new \DateTimeImmutable();
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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;
        return $this;
    }

    public function getPoids(): ?string
    {
        return $this->poids;
    }

    public function setPoids(string $poids): static
    {
        $this->poids = $poids;
        return $this;
    }

    public function getSante(): ?string
    {
        return $this->sante;
    }

    public function setSante(string $sante): static
    {
        $this->sante = $sante;
        return $this;
    }

    public function getDateEntree(): ?\DateTimeImmutable
    {
        return $this->dateEntree;
    }

    public function setDateEntree(\DateTimeImmutable $dateEntree): static
    {
        $this->dateEntree = $dateEntree;
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
}
