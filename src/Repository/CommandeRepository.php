<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Commande> */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    /**
     * QueryBuilder de base avec jointures pour éviter N+1 :
     * - client (pour les infos utilisateur)
     * - details + produit (pour le détail de la commande)
     * - livraison (pour l'adresse de livraison)
     */
    private function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->addSelect('cl', 'd', 'p', 'l')
            ->leftJoin('c.client', 'cl')
            ->leftJoin('c.details', 'd')
            ->leftJoin('d.produit', 'p')
            ->leftJoin('c.livraison', 'l');
    }

    /** @return array<Commande> */
    public function findByClientOrdered(Client $client): array
    {
        return $this->createBaseQueryBuilder()
            ->where('c.client = :client')
            ->setParameter('client', $client)
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return array<Commande> */
    public function findAllOrdered(): array
    {
        return $this->createBaseQueryBuilder()
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return array<Commande> */
    public function findByStatut(string $statut): array
    {
        return $this->createBaseQueryBuilder()
            ->where('c.statut = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère une commande unique avec toutes ses jointures.
     */
    public function findWithDetails(int $id): ?Commande
    {
        return $this->createBaseQueryBuilder()
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Dashboard admin : commandes récentes avec jointures pour les stats.
     *
     * @return array<Commande>
     */
    public function findRecentForDashboard(int $limit = 10): array
    {
        return $this->createBaseQueryBuilder()
            ->orderBy('c.dateCommande', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByStatut(string $statut): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.statut = :statut')
            ->setParameter('statut', $statut)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Calcule le chiffre d'affaires total (commandes confirmées et livrées).
     */
    public function sumMontantTotalByStatuts(array $statuts): float
    {
        $result = $this->createQueryBuilder('c')
            ->select('SUM(c.montantTotal)')
            ->where('c.statut IN (:statuts)')
            ->setParameter('statuts', $statuts)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0.0);
    }
}
