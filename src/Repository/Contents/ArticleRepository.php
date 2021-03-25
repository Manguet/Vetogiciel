<?php

namespace App\Repository\Contents;

use App\Entity\Contents\Article;
use App\Entity\Contents\ArticleCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    /**
     * @return int|mixed|string
     */
    public function findByArticlePriority()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.priority', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param ArticleCategory $category
     *
     * @return int|mixed|string
     */
    public function findByCategory(ArticleCategory $category)
    {
        return $this->createQueryBuilder('a')
            ->where('a.articleCategory = :category')
            ->orderBy('a.priority')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param ArticleCategory $category
     *
     * @param int $id
     * @return int|mixed|string
     */
    public function findOthersByCategory(ArticleCategory $category, int $id)
    {
        return $this->createQueryBuilder('a')
            ->where('a.articleCategory = :category')
            ->andWhere('a.id != :id')
            ->orderBy('a.priority')
            ->setParameters([
                'category' => $category,
                'id'       => $id,
            ])
            ->getQuery()
            ->getResult()
        ;
    }
}
