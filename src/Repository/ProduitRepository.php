<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Nourriture;
use App\Entity\Oeuf;
use App\Entity\Produit;
use App\Entity\Ticket;
use App\Entity\Viande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Produit> */
class ProduitRepository extends ServiceEntityRepository
{
    private const TYPE_MAP = [
        'oeuf' => Oeuf::class,
        'viande' => Viande::class,
        'ticket' => Ticket::class,
        'nourriture' => Nourriture::class,
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * QueryBuilder de base avec jointure sur stock pour éviter N+1.
     */
    private function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->addSelect('s')
            ->leftJoin('p.stock', 's');
    }

    /** @return array<Produit> */
    public function findAllOrdered(): array
    {
        return $this->createBaseQueryBuilder()
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return array<Produit> */
    public function findByType(string $type): array
    {
        $className = self::TYPE_MAP[$type] ?? null;

        if (null === $className) {
            return [];
        }

        return $this->createBaseQueryBuilder()
            ->where('p INSTANCE OF ' . $className)
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return array<Produit> */
    public function findDisponibles(): array
    {
        return $this->createBaseQueryBuilder()
            ->where('s.quantiteDisponible > 0')
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère un produit avec son stock en une seule requête.
     */
    public function findWithStock(int $id): ?Produit
    {
        return $this->createBaseQueryBuilder()
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Retourne le nombre total de produits (pas d'hydration, COUNT simple).
     */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
