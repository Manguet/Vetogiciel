<?php

namespace App\Repository\Mail;

use App\Entity\Mail\MailMessage;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class MailMessageRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MailMessage::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findAvailableMessage(DateTimeImmutable $redeliverTimeout)
    {
        return $this->createQueryBuilder('m')
            ->where('m.deliveredAt is NULL')
            ->orWhere('m.deliveredAt < :redeliverTimeout')
            ->setParameter('redeliverTimeout', $redeliverTimeout)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}