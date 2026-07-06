<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'incubations')]
class Incubation
{
    use UuidEntityTrait;

    #[ORM\ManyToOne(inversedBy: 'incubations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Couveuse $couveuse = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateFinPrevue = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateFinReelle = null;

    #[Assert\NotNull(message: 'Le nombre d\'œufs au début est obligatoire.')]
    #[Assert\Positive(message: 'Le nombre d\'œufs au début doit être positif.')]
    #[ORM\Column]
    private ?int $nbOeufsDebut = null;

    #[ORM\Column]
    private int $nbOeufsEclos = 0;

    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[Assert\Choice(choices: ['en_cours', 'terminee', 'echec'], message: 'Le statut sélectionné n\'est pas valide.')]
    #[ORM\Column(length: 50)]
    private ?string $statut = 'en_cours';

    /** @var Collection<int, Naissance> */
    #[ORM\OneToMany(targetEntity: Naissance::class, mappedBy: 'incubation')]
    private Collection $naissances;

    public function __construct()
    {
        $this->naissances = new ArrayCollection();
        $this->dateDebut = new \DateTimeImmutable();
    }

    public function getCouveuse(): ?Couveuse
    {
        return $this->couveuse;
    }

    public function setCouveuse(?Couveuse $couveuse): static
    {
        $this->couveuse = $couveuse;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeImmutable $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFinPrevue(): ?\DateTimeImmutable
    {
        return $this->dateFinPrevue;
    }

    public function setDateFinPrevue(?\DateTimeImmutable $dateFinPrevue): static
    {
        $this->dateFinPrevue = $dateFinPrevue;
        return $this;
    }

    public function getDateFinReelle(): ?\DateTimeImmutable
    {
        return $this->dateFinReelle;
    }

    public function setDateFinReelle(?\DateTimeImmutable $dateFinReelle): static
    {
        $this->dateFinReelle = $dateFinReelle;
        return $this;
    }

    public function getNbOeufsDebut(): ?int
    {
        return $this->nbOeufsDebut;
    }

    public function setNbOeufsDebut(int $nbOeufsDebut): static
    {
        $this->nbOeufsDebut = $nbOeufsDebut;
        return $this;
    }

    public function getNbOeufsEclos(): int
    {
        return $this->nbOeufsEclos;
    }

    public function setNbOeufsEclos(int $nbOeufsEclos): static
    {
        $this->nbOeufsEclos = $nbOeufsEclos;
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

    /** @return Collection<int, Naissance> */
    public function getNaissances(): Collection
    {
        return $this->naissances;
    }

    public function addNaissance(Naissance $naissance): static
    {
        if (!$this->naissances->contains($naissance)) {
            $this->naissances->add($naissance);
            $naissance->setIncubation($this);
        }

        return $this;
    }

    public function removeNaissance(Naissance $naissance): static
    {
        $this->naissances->removeElement($naissance);
        return $this;
    }
}
