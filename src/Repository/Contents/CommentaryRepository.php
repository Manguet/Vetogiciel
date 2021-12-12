<?php

namespace App\Repository\Contents;

use App\Entity\Contents\Article;
use App\Entity\Contents\Commentary;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class CommentaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentary::class);
    }

    /**
     * @param UserInterface $createdBy
     * @param Article $article
     *
     * @return int|mixed|string
     */
    public function findLastHourCommentary(UserInterface $createdBy, Article $article)
    {
        $date = new DateTime('now');
        $toSub = new DateInterval('PT1H');
        $lastHour = $date->sub($toSub);

        return $this->createQueryBuilder('c')
            ->where('c.article = :article')
            ->andWhere('c.createdByVeterinary = :user')
            ->orWhere('c.createdByEmployee = :user')
            ->orWhere('c.createdByClient = :user')
            ->andWhere('c.dateCreation > :lasthour')
            ->setParameters([
                'article'  => $article,
                'user'     => $createdBy,
                'lasthour' => $lastHour,
            ])
            ->getQuery()
            ->getResult()
        ;
    }
}