<?php

namespace App\Repository\Contents;

use App\Entity\Contents\Article;
use App\Entity\Contents\Commentary;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * CommentRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentary::class);
    }

    /**
     * @param string $createdBy
     * @param Article $article
     *
     * @return int|mixed|string
     */
    public function findLastHourCommentary(string $createdBy, Article $article)
    {
        $lastHour = new DateTime('now');
        $lastHour->modify('-1 hour');

        return $this->createQueryBuilder('c')
            ->where('c.article = :article')
            ->andWhere('c.dateCreation >= :lasthour')
            ->andWhere('c.createdBy = :createdBy')
            ->setParameters([
                'article'    => $article,
                'lasthour'   => $lastHour,
                'createdBy'  => $createdBy,
            ])
            ->getQuery()
            ->getResult()
        ;
    }
}
