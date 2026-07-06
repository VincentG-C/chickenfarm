<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidEntityTrait;
use App\Repository\CouveuseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CouveuseRepository::class)]
#[ORM\Table(name: 'couveuses')]
class Couveuse
{
    use UuidEntityTrait;

    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 100, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[Assert\NotNull(message: 'La capacité est obligatoire.')]
    #[Assert\Positive(message: 'La capacité doit être positive.')]
    #[ORM\Column]
    private ?int $capacite = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $temperature = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $humidite = null;

    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[Assert\Choice(choices: ['disponible', 'en_marche', 'maintenance', 'hors_service'], message: 'Le statut sélectionné n\'est pas valide.')]
    #[ORM\Column(length: 50)]
    private ?string $statut = 'disponible';

    /** @var Collection<int, Incubation> */
    #[ORM\OneToMany(targetEntity: Incubation::class, mappedBy: 'couveuse')]
    private Collection $incubations;

    public function __construct()
    {
        $this->incubations = new ArrayCollection();
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

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;
        return $this;
    }

    public function getTemperature(): ?string
    {
        return $this->temperature;
    }

    public function setTemperature(?string $temperature): static
    {
        $this->temperature = $temperature;
        return $this;
    }

    public function getHumidite(): ?string
    {
        return $this->humidite;
    }

    public function setHumidite(?string $humidite): static
    {
        $this->humidite = $humidite;
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

    /** @return Collection<int, Incubation> */
    public function getIncubations(): Collection
    {
        return $this->incubations;
    }

    public function addIncubation(Incubation $incubation): static
    {
        if (!$this->incubations->contains($incubation)) {
            $this->incubations->add($incubation);
            $incubation->setCouveuse($this);
        }

        return $this;
    }

    public function removeIncubation(Incubation $incubation): static
    {
        $this->incubations->removeElement($incubation);
        return $this;
    }
}
