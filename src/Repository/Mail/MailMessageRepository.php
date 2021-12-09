<?php

namespace App\Repository\Mail;

use App\Entity\Calendar\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class MailMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }
}