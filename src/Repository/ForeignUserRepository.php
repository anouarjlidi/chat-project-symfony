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
}
