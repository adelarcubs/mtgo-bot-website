<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CardSet;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @method CardSet|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardSet|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardSet[]    findAll()
 * @method CardSet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardSetRepository extends EntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(CardSet::class));
        $this->entityManager = $entityManager;
    }

    public function save(CardSet $cardSet, bool $flush = true): void
    {
        $this->entityManager->persist($cardSet);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(CardSet $cardSet): void
    {
        $this->entityManager->remove($cardSet);
        $this->entityManager->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findOneByCode(string $code): ?CardSet
    {
        return $this->findOneBy(['code' => $code]);
    }
}
