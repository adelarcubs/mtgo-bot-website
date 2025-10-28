<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cart;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends EntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $em, ?ClassMetadata $class = null)
    {
        parent::__construct($em, $class ?? $em->getClassMetadata(Cart::class));
        $this->entityManager = $em;
    }

    public function save(Cart $entity, bool $flush = true): void
    {
        $entity->setUpdatedAt(new DateTimeImmutable());
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(Cart $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function findOneByUser(int $userId): ?Cart
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOrCreateForUser(int $userId): Cart
    {
        $cart = $this->findOneByUser($userId);

        if ($cart === null) {
            $cart = new Cart($userId);
            $this->save($cart);
        }

        return $cart;
    }
}
