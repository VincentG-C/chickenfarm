<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Enclos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Enclos> */
class EnclosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enclos::class);
    }

    /**
     * QueryBuilder de base avec jointure sur les gallinacés pour éviter N+1.
     */
    private function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->addSelect('g')
            ->leftJoin('e.gallinaces', 'g');
    }

    /** @return array<Enclos> */
    public function findAllOrdered(): array
    {
        return $this->createBaseQueryBuilder()
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère un enclos avec ses gallinacés en une seule requête.
     */
    public function findWithGallinaces(int $id): ?Enclos
    {
        return $this->createBaseQueryBuilder()
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
