<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\JournalFerme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<JournalFerme> */
class JournalFermeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JournalFerme::class);
    }

    /** @return array<JournalFerme> */
    public function findRecent(int $limit = 20): array
    {
        return $this->createQueryBuilder('j')
            ->orderBy('j.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /** @return array<JournalFerme> */
    public function findByDateRange(\DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        return $this->createQueryBuilder('j')
            ->where('j.date >= :start AND j.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('j.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function sumQuantiteByTypeAndDate(string $type, \DateTimeImmutable $date): int
    {
        $result = $this->createQueryBuilder('j')
            ->select('SUM(j.quantite)')
            ->where('j.type = :type AND j.date = :date')
            ->setParameter('type', $type)
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result;
    }
}
