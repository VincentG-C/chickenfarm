<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Viande extends Produit
{
    #[Assert\NotNull(message: 'Le poids moyen est obligatoire.')]
    #[Assert\Positive(message: 'Le poids moyen doit être positif.')]
    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private ?string $poidsMoyen = null;

    #[Assert\NotBlank(message: 'Le type de découpe est obligatoire.')]
    #[Assert\Choice(choices: ['entier', 'filet', 'cuisse', 'aile', 'blanc'], message: 'Le type de découpe sélectionné n\'est pas valide.')]
    #[ORM\Column(length: 50)]
    private ?string $typeDecoupe = null;

    public function getPoidsMoyen(): ?string
    {
        return $this->poidsMoyen;
    }

    public function setPoidsMoyen(string $poidsMoyen): static
    {
        $this->poidsMoyen = $poidsMoyen;
        return $this;
    }

    public function getTypeDecoupe(): ?string
    {
        return $this->typeDecoupe;
    }

    public function setTypeDecoupe(string $typeDecoupe): static
    {
        $this->typeDecoupe = $typeDecoupe;
        return $this;
    }
}
