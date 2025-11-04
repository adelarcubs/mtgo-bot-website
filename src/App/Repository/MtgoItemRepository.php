<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MtgoItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @method MtgoItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method MtgoItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method MtgoItem[]    findAll()
 * @method MtgoItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MtgoItemRepository extends EntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(MtgoItem::class));
        $this->entityManager = $entityManager;
    }

    public function save(MtgoItem $mtgoItem, bool $flush = true): void
    {
        $this->entityManager->persist($mtgoItem);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(MtgoItem $mtgoItem): void
    {
        $this->entityManager->remove($mtgoItem);
        $this->entityManager->flush();
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
