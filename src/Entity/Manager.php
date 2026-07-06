<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Manager extends User
{
    #[Assert\Length(max: 50, maxMessage: 'Le téléphone ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $telephone = null;

    #[Assert\NotBlank(message: 'Le code employé est obligatoire.')]
    #[Assert\Length(max: 50, maxMessage: 'Le code employé ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(length: 50, unique: true)]
    private ?string $codeEmploye = null;

    /** @var Collection<int, JournalFerme> */
    #[ORM\OneToMany(targetEntity: JournalFerme::class, mappedBy: 'manager')]
    private Collection $journalEntries;

    public function __construct()
    {
        parent::__construct();
        $this->journalEntries = new ArrayCollection();
        $this->addAssignedRole('ROLE_MANAGER');
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getCodeEmploye(): ?string
    {
        return $this->codeEmploye;
    }

    public function setCodeEmploye(string $codeEmploye): static
    {
        $this->codeEmploye = $codeEmploye;

        return $this;
    }

    /** @return Collection<int, JournalFerme> */
    public function getJournalEntries(): Collection
    {
        return $this->journalEntries;
    }

    public function addJournalEntry(JournalFerme $journalEntry): static
    {
        if (!$this->journalEntries->contains($journalEntry)) {
            $this->journalEntries->add($journalEntry);
            $journalEntry->setManager($this);
        }

        return $this;
    }

    public function removeJournalEntry(JournalFerme $journalEntry): static
    {
        $this->journalEntries->removeElement($journalEntry);

        return $this;
    }
}
