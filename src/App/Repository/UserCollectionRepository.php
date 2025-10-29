<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @method UserCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCollection[]    findAll()
 * @method UserCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCollectionRepository extends EntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $em, ?ClassMetadata $class = null)
    {
        parent::__construct($em, $class ?? $em->getClassMetadata(UserCollection::class));
        $this->entityManager = $em;
    }

    /**
     * @return UserCollection[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('uc')
            ->andWhere('uc.user = :user')
            ->setParameter('user', $user)
            ->orderBy('uc.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(UserCollection $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(UserCollection $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
