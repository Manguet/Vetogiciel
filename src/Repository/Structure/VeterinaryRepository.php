<?php

namespace App\Repository\Structure;

use App\Entity\Structure\Veterinary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Veterinary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Veterinary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Veterinary[]    findAll()
 * @method Veterinary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VeterinaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Veterinary::class);
    }
}
