<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserCollectionItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @method UserCollectionItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCollectionItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCollectionItem[]    findAll()
 * @method UserCollectionItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCollectionItemRepository extends EntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $em, ?ClassMetadata $class = null)
    {
        parent::__construct($em, $class ?? $em->getClassMetadata(UserCollectionItem::class));
        $this->entityManager = $em;
    }

    /**
     * @return UserCollectionItem[]
     */
    /**
     * @return UserCollectionItem[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('uci')
            ->andWhere('uci.user = :user')
            ->setParameter('user', $user)
            ->orderBy('uci.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return UserCollectionItem[]
     */
    public function findNonZeroQuantityByUser(User $user): array
    {
        return $this->createQueryBuilder('uci')
            ->where('uci.user = :user')
            ->andWhere('uci.quantity > 0')
            ->setParameter('user', $user)
            ->orderBy('uci.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(UserCollectionItem $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function resetUserCollection(User $user): void
    {
        $this->createQueryBuilder('uci')
            ->update()
            ->set('uci.quantity', 0)
            ->where('uci.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function remove(UserCollectionItem $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
