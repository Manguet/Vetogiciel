<?php

namespace App\Repository\Contents;

use App\Entity\Contents\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @param $value number of results needed
     *
     * @return int|mixed|string
     */
    public function findByMax($value)
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.priority', 'ASC')
            ->setMaxResults($value)
            ->getQuery()
            ->getResult()
        ;
    }
}
