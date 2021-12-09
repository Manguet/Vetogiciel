<?php

namespace App\Repository\Patients;

use App\Entity\Patients\Race;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}