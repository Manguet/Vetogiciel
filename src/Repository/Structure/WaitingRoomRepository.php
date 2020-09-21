<?php

namespace App\Repository\Structure;

use App\Entity\Structure\WaitingRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method WaitingRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method WaitingRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method WaitingRoom[]    findAll()
 * @method WaitingRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WaitingRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WaitingRoom::class);
    }
}
