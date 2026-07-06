<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Couveuse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Couveuse> */
class CouveuseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Couveuse::class);
    }

    /**
     * QueryBuilder de base avec jointure sur les incubations pour éviter N+1.
     */
    private function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->addSelect('i')
            ->leftJoin('c.incubations', 'i');
    }

    /** @return array<Couveuse> */
    public function findAll(): array
    {
        return $this->createBaseQueryBuilder()
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return array<Couveuse> */
    public function findDisponibles(): array
    {
        return $this->createBaseQueryBuilder()
            ->where('c.statut = :statut')
            ->setParameter('statut', 'disponible')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère une couveuse avec ses incubations en une seule requête.
     */
    public function findWithIncubations(int $id): ?Couveuse
    {
        return $this->createBaseQueryBuilder()
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
