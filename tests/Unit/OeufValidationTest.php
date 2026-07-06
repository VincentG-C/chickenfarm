<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Enclos;
use App\Entity\Oeuf;
use App\Entity\Poule;
use PHPUnit\Framework\Attributes as PHPUnit;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Test unitaire de l'entité Oeuf : validation des contraintes métier.
 */
#[PHPUnit\CoversClass(Oeuf::class)]
class OeufValidationTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Un œuf valide (tous les champs obligatoires corrects) ne doit
     * générer aucune violation de validation.
     */
    #[PHPUnit\Test]
    public function testValidOeufProducesNoViolations(): void
    {
        $oeuf = $this->createValidOeuf();
        $violations = $this->validator->validate($oeuf);

        self::assertCount(0, $violations, 'Un œuf valide ne doit pas avoir de violations.');
    }

    /**
     * Un œuf avec un calibre invalide doit échouer la validation.
     */
    #[PHPUnit\Test]
    public function testInvalidCalibre(): void
    {
        $oeuf = $this->createValidOeuf();
        $oeuf->setCalibre('geant'); // non autorisé

        $violations = $this->validator->validate($oeuf);
        $calibreViolations = $this->getViolationsByProperty($violations, 'calibre');

        self::assertCount(1, $calibreViolations, 'Le calibre "geant" doit être rejeté.');
    }

    /**
     * La création d'un œuf avec des valeurs extrêmes (calibre valide) ne doit
     * pas lever d'exception et l'objet doit être correctement hydraté.
     */
    #[PHPUnit\Test]
    public function testOeufHydration(): void
    {
        $oeuf = new Oeuf();
        $oeuf->setNom('Œufs extra-frais');
        $oeuf->setPrix('5.50');
        $oeuf->setCalibre('extra_gros');
        $oeuf->setEstFeconde(false);
        $oeuf->setDatePonte(new \DateTimeImmutable('2026-07-01'));

        self::assertSame('Œufs extra-frais', $oeuf->getNom());
        self::assertSame('5.50', $oeuf->getPrix());
        self::assertSame('extra_gros', $oeuf->getCalibre());
        self::assertFalse($oeuf->isEstFeconde());
        self::assertEquals('2026-07-01', $oeuf->getDatePonte()?->format('Y-m-d'));
    }

    /**
     * Vérifie la relation entre une Poule et ses Oeufs.
     */
    #[PHPUnit\Test]
    public function testOeufPouleRelation(): void
    {
        $enclos = new Enclos();
        $enclos->setNom('Test');
        $enclos->setSuperficie('100');
        $enclos->setCapaciteMax(10);
        $enclos->setType('poulailler');

        $poule = new Poule();
        $poule->setNom('Blanche');
        $poule->setAge(12);
        $poule->setPoids('2.0');
        $poule->setSante('bon');
        $poule->setEnclos($enclos);
        $poule->setCyclePonte(5);

        $oeuf = $this->createValidOeuf();
        $oeuf->setPoule($poule);
        $poule->addOeuf($oeuf);

        self::assertSame($poule, $oeuf->getPoule(), 'L\'œuf doit référencer la poule.');
        self::assertCount(1, $poule->getOeufs(), 'La poule doit avoir 1 œuf.');
        self::assertTrue($poule->getOeufs()->contains($oeuf), 'La collection doit contenir l\'œuf.');
    }

    // ── Helpers ──────────────────────────────────────

    private function createValidOeuf(): Oeuf
    {
        $oeuf = new Oeuf();
        $oeuf->setNom('Œufs frais');
        $oeuf->setPrix('3.50');
        $oeuf->setCalibre('moyen');
        $oeuf->setEstFeconde(false);

        return $oeuf;
    }

    /**
     * @return list<\Symfony\Component\Validator\ConstraintViolation>
     */
    private function getViolationsByProperty(\Symfony\Component\Validator\ConstraintViolationListInterface $violations, string $property): array
    {
        $filtered = [];
        foreach ($violations as $violation) {
            if ($violation->getPropertyPath() === $property) {
                /** @var \Symfony\Component\Validator\ConstraintViolation $v */
                $v = $violation;
                $filtered[] = $v;
            }
        }

        return $filtered;
    }
}
