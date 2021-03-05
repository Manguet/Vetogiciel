<?php

namespace App\Repository\Patients;

use App\Entity\Patients\Race;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class RaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Race::class);
    }

    /**
     * @return QueryBuilder for form entityType
     * Get all Races ordered by name
     */
    public function findAllByName(): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.name', 'ASC');
    }

    /**
     * Get all Races ordered by name
     * @param null $q
     *
     * @return int|mixed|string
     */
    public function findAllByNameResults($q = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.name', 'ASC');

        if ($q) {
            $qb
                ->andWhere('r.name LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
