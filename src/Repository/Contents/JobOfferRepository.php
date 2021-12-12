<?php

namespace App\Repository\Contents;

use App\Entity\Contents\JobOffer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class JobOfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobOffer::class);
    }

    /**
     * @return int|mixed|string
     */
    public function findByOfferPriority()
    {
        return $this->createQueryBuilder('j')
            ->orderBy('j.priority', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}
