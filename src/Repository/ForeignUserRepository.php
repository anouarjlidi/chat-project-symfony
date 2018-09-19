<?php

namespace App\Repository;

use App\Entity\ForeignUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ForeignUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForeignUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForeignUser[]    findAll()
 * @method ForeignUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForeignUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ForeignUser::class);
    }

    public function getForeignUsersWithUserIdAndTempId(string $site_id, array $arrayUserUserIds, array $arrayTempUserIds)
    {
        foreach ($arrayUserUserIds as $key => $foreignUserId) {
            if ($foreignUserId == null) unset($arrayUserUserIds[$key]);
        }
        foreach ($arrayTempUserIds as $key => $foreignTempId) {
            if ($foreignTempId == null) unset($arrayTempUserIds[$key]);
        }
        $qb = $this->createQueryBuilder('fu');
        if (sizeof($arrayUserUserIds) == 0 AND sizeof($arrayTempUserIds) != 0) {
            $qb
                ->where($qb->expr()->in('fu.tempId', $arrayTempUserIds));
        } elseif (sizeof($arrayUserUserIds) != 0 AND sizeof($arrayTempUserIds) == 0) {
            $qb
                ->where($qb->expr()->in('fu.userId', $arrayUserUserIds));
        } else {
            $qb
                ->where($qb->expr()->in('fu.userId', $arrayUserUserIds))
                ->orWhere($qb->expr()->in('fu.tempId', $arrayTempUserIds));
        }
        $qb
            ->andWhere('fu.webSite = :site_id')
            ->setParameter('site_id', $site_id);
        $foreignUsers = $qb->getQuery()->getResult();
        return $foreignUsers;
    }
}
