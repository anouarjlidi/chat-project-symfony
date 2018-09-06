<?php

namespace App\Repository;

use App\Entity\WebSite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WebSite|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebSite|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebSite[]    findAll()
 * @method WebSite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebSiteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WebSite::class);
    }

//    /**
//     * @return WebSite[] Returns an array of WebSite objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WebSite
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
