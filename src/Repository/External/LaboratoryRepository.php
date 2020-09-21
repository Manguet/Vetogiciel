<?php

namespace App\Repository\External;

use App\Entity\External\Laboratory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Laboratory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Laboratory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Laboratory[]    findAll()
 * @method Laboratory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LaboratoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Laboratory::class);
    }

}
