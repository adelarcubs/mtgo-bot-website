<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MtgoItem;
use App\Entity\RentedCard;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @method RentedCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method RentedCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method RentedCard[]    findAll()
 * @method RentedCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RentedCardRepository extends EntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $em, ?ClassMetadata $class = null)
    {
        parent::__construct($em, $class ?? $em->getClassMetadata(RentedCard::class));
        $this->entityManager = $em;
    }

    public function save(RentedCard $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(RentedCard $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function findRentedCardsByUser(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.quantity > 0')
            ->setParameter('user', $user)
            ->orderBy('r.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCardByUserAndMtgoItem(User $user, MtgoItem $mtgoItem): ?RentedCard
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.mtgoItem = :mtgoItem')
            ->setParameter('user', $user)
            ->setParameter('mtgoItem', $mtgoItem)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findRentedCardsByUserWithDetails(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'm', 'c')
            ->join('r.mtgoItem', 'm')
            ->leftJoin('m.cardSet', 'c')
            ->andWhere('r.user = :user')
            ->andWhere('r.quantity > 0')
            ->setParameter('user', $user)
            ->orderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
