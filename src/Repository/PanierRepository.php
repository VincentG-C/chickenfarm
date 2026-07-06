<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Panier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Panier> */
class PanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panier::class);
    }

    /**
     * QueryBuilder de base avec jointure sur les produits du panier et leur stock.
     */
    private function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->addSelect('pp', 'prod', 's')
            ->leftJoin('p.produits', 'pp')
            ->leftJoin('pp.produit', 'prod')
            ->leftJoin('prod.stock', 's');
    }

    /**
     * Récupère un panier avec ses produits et leur stock en une seule requête.
     */
    public function findOneByClient(Client $client): ?Panier
    {
        return $this->createBaseQueryBuilder()
            ->where('p.client = :client')
            ->setParameter('client', $client)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
