<?php

namespace App\Repository\Contents;

use App\Entity\Contents\JobOfferType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class JobOfferTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobOfferType::class);
    }

    /**
     * Get all ArticleCategory ordered by title
     * @param null $q
     *
     * @return int|mixed|string
     */
    public function findAllByTitleResults($q = null)
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.title', 'ASC');

        if ($q) {
            $qb
                ->andWhere('s.title LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
