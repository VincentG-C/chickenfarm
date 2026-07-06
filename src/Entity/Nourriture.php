<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Nourriture extends Produit
{
    #[Assert\NotBlank(message: 'Le type d\'aliment est obligatoire.')]
    #[Assert\Choice(choices: ['granules', 'graines', 'vitamines', 'calcium', 'autre'], message: 'Le type d\'aliment sélectionné n\'est pas valide.')]
    #[ORM\Column(length: 50)]
    private ?string $typeAliment = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $composition = null;

    public function getTypeAliment(): ?string
    {
        return $this->typeAliment;
    }

    public function setTypeAliment(string $typeAliment): static
    {
        $this->typeAliment = $typeAliment;
        return $this;
    }

    public function getComposition(): ?string
    {
        return $this->composition;
    }

    public function setComposition(?string $composition): static
    {
        $this->composition = $composition;
        return $this;
    }
}
