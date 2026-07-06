<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Poule extends Gallinace
{
    #[ORM\Column]
    private int $cyclePonte = 0;

    /** @var Collection<int, Oeuf> */
    #[ORM\OneToMany(targetEntity: Oeuf::class, mappedBy: 'poule')]
    private Collection $oeufs;

    public function __construct()
    {
        parent::__construct();
        $this->oeufs = new ArrayCollection();
    }

    public function getCyclePonte(): int
    {
        return $this->cyclePonte;
    }

    public function setCyclePonte(int $cyclePonte): static
    {
        $this->cyclePonte = $cyclePonte;
        return $this;
    }

    /** @return Collection<int, Oeuf> */
    public function getOeufs(): Collection
    {
        return $this->oeufs;
    }

    public function addOeuf(Oeuf $oeuf): static
    {
        if (!$this->oeufs->contains($oeuf)) {
            $this->oeufs->add($oeuf);
            $oeuf->setPoule($this);
        }

        return $this;
    }

    public function removeOeuf(Oeuf $oeuf): static
    {
        $this->oeufs->removeElement($oeuf);
        return $this;
    }
}
