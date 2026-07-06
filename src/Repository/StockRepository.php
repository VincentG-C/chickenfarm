<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Stock> */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    /**
     * QueryBuilder de base avec jointure sur le produit.
     */
    private function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->addSelect('p')
            ->leftJoin('s.produit', 'p');
    }

    /** @return array<Stock> */
    public function findAll(): array
    {
        return $this->createBaseQueryBuilder()
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Stocks sous le seuil d'alerte, avec infos produit.
     */
    public function findBelowThreshold(): array
    {
        return $this->createBaseQueryBuilder()
            ->where('s.quantiteDisponible < s.seuilAlerte')
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère un stock avec son produit.
     */
    public function findWithProduit(int $id): ?Stock
    {
        return $this->createBaseQueryBuilder()
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Stock d'un produit spécifique.
     */
    public function findOneByProduitId(int $produitId): ?Stock
    {
        return $this->createBaseQueryBuilder()
            ->where('IDENTITY(s.produit) = :produitId')
            ->setParameter('produitId', $produitId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
