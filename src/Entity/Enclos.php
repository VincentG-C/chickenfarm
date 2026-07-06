<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidEntityTrait;
use App\Repository\EnclosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EnclosRepository::class)]
#[ORM\Table(name: 'enclos')]
class Enclos
{
    use UuidEntityTrait;

    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 100, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[Assert\NotNull(message: 'La superficie est obligatoire.')]
    #[Assert\Positive(message: 'La superficie doit être positive.')]
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $superficie = null;

    #[Assert\NotNull(message: 'La capacité maximale est obligatoire.')]
    #[Assert\Positive(message: 'La capacité maximale doit être positive.')]
    #[ORM\Column]
    private ?int $capaciteMax = null;

    #[Assert\NotBlank(message: 'Le type est obligatoire.')]
    #[Assert\Choice(choices: ['poulailler', 'enclos_exterieur', 'voliere'], message: 'Le type sélectionné n\'est pas valide.')]
    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column]
    private int $nbGallinaces = 0;

    /** @var Collection<int, Gallinace> */
    #[ORM\OneToMany(targetEntity: Gallinace::class, mappedBy: 'enclos')]
    private Collection $gallinaces;

    public function __construct()
    {
        $this->gallinaces = new ArrayCollection();
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

    public function getSuperficie(): ?string
    {
        return $this->superficie;
    }

    public function setSuperficie(string $superficie): static
    {
        $this->superficie = $superficie;
        return $this;
    }

    public function getCapaciteMax(): ?int
    {
        return $this->capaciteMax;
    }

    public function setCapaciteMax(int $capaciteMax): static
    {
        $this->capaciteMax = $capaciteMax;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getNbGallinaces(): int
    {
        return $this->nbGallinaces;
    }

    public function setNbGallinaces(int $nbGallinaces): static
    {
        $this->nbGallinaces = $nbGallinaces;
        return $this;
    }

    /** @return Collection<int, Gallinace> */
    public function getGallinaces(): Collection
    {
        return $this->gallinaces;
    }

    public function addGallinace(Gallinace $gallinace): static
    {
        if (!$this->gallinaces->contains($gallinace)) {
            $this->gallinaces->add($gallinace);
            $gallinace->setEnclos($this);
        }

        return $this;
    }

    public function removeGallinace(Gallinace $gallinace): static
    {
        $this->gallinaces->removeElement($gallinace);
        return $this;
    }
}
