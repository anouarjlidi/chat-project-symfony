<?php

namespace App\Repository;

use App\Entity\ChatRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ChatRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatRoom[]    findAll()
 * @method ChatRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRoomRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ChatRoom::class);
    }

    public function getChatRoomWithUsers(string $chatType, array $foreignUserIds)
    {
        $qb = $this->createQueryBuilder('ch');
        $qb
            ->where('ch.chatType = :chatType')
            ->setParameter('chatType', $chatType)
            ->where(':foreignUser MEMBER OF ch.foreignUsers')
            ->setParameters(array('foreignUser' => $foreignUserIds))
            ->setMaxResults(1);
        $chatRoom = $qb->getQuery()->getResult();
        if (is_array($chatRoom) AND sizeof($chatRoom) == 1) {
            $chatRoom = $chatRoom[0];
        }
        return $chatRoom;
    }
}
