<?php

namespace App\Repository\Patients;

use App\Entity\Patients\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AnimalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animal::class);
    }

    /**
     * Get all Clients ordered by name
     * @param null $q
     *
     * @return int|mixed|string
     */
    public function findAllByNameResults($q = null, $client = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.name', 'ASC');

        if ($q) {
            $qb
                ->where('a.name LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        if ($client) {
            $qb
                ->andWhere('a.client = :client')
                ->setParameter('client', $client);
        }

        return $qb->getQuery()->getResult();
    }
}