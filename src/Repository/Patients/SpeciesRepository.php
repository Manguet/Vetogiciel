<?php

namespace App\Repository\Patients;

use App\Entity\Patients\Species;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class SpeciesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Species::class);
    }

    /**
     * @return QueryBuilder for form entityType
     * Get all Races ordered by name
     */
    public function findAllByName(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC');
    }

    /**
     * Get all Species ordered by name
     *
     * @param null $q
     * @param null $raceId
     *
     * @return int|mixed|string
     */
    public function findAllByNameResults($q = null, $raceId = null)
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC');

        if ($q) {
            $qb
                ->andWhere('s.name LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        if ($raceId) {
            $qb
                ->andWhere('s.race = :raceId')
                ->setParameter('raceId', $raceId);
        }

        return $qb->getQuery()->getResult();
    }
}