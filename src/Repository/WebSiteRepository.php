<?php

namespace App\Repository;

use App\Entity\User;
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

    public function getInstalledWebSitesForUser(User $user)
    {
        $qb = $this->createQueryBuilder("w")
            ->where(':user MEMBER OF w.users')
            ->setParameters(array('user' => $user))
            ->andWhere('w.installed = :val')
            ->setParameter('val', true);
        return $qb->getQuery()->getResult();
    }
}
