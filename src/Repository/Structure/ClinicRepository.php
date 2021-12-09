<?php

namespace App\Repository\Structure;

use App\Entity\Structure\Clinic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ClinicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clinic::class);
    }

    /**
     * Get all Structures ordered by name
     * @param null $q
     *
     * @return int|mixed|string
     */
    public function findAllByNameResults($q = null)
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC');

        if ($q) {
            $qb
                ->andWhere('s.name LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function findClinicsByPriority()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.priority', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}