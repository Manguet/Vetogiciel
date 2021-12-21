<?php

namespace App\Repository\Structure;

use App\Entity\Structure\Vat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class VatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vat::class);
    }

    /**
     * @param null $q
     *
     * @return int|mixed|string
     */
    public function findAllByNameResults($q = null)
    {
        $qb = $this->createQueryBuilder('v')
            ->orderBy('v.title', 'ASC');

        if ($q) {
            $qb
                ->orWhere('v.title LIKE :q')
                ->orWhere('v.code LIKE :s')
                ->setParameter('q', '%' . $q . '%');
        }

        return $qb->getQuery()->getResult();
    }
}