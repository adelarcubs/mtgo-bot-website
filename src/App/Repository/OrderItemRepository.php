<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OrderItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/** @template-extends EntityRepository<OrderItem> */
class OrderItemRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(OrderItem::class));
    }

    /** @return OrderItem[] */
    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        $limit = null,
        $offset = null
    ): array {
        /** @var OrderItem[] $result */
        $result = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $result;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?OrderItem
    {
        /** @var OrderItem|null $result */
        $result = parent::findOneBy($criteria, $orderBy);
        return $result;
    }
}
