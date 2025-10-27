<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends EntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $em, ?ClassMetadata $class = null)
    {
        parent::__construct($em, $class ?? $em->getClassMetadata(Order::class));
        $this->entityManager = $em;
    }

    public function save(Order $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(Order $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @return Order[] Returns an array of Order objects for a specific user
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.userId = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
