<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    private function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC');
    }

    /** @return list<User> */
    public function findAll(): array
    {
        return $this->createBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * Liste des clients avec leurs commandes (jointure pour éviter N+1).
     *
     * @return list<Client>
     */
    public function findClientsWithCommandes(): array
    {
        return $this->createQueryBuilder('u')
            ->addSelect('c')
            ->leftJoin('u.commandes', 'c')
            ->where('u INSTANCE OF ' . Client::class)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte les utilisateurs ayant un rôle spécifique.
     * Utilise une requête SQL native car le champ roles est de type JSON
     * en PostgreSQL et ne supporte pas LIKE via DQL.
     */
    public function countByRole(string $role): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT COUNT(*) FROM users WHERE roles::text LIKE :role';

        return (int) $conn->fetchOne($sql, [
            'role' => '%"' . $role . '"%',
        ]);
    }
}
