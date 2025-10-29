<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/** @template-extends EntityRepository<CartItem> */
class CartItemRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new ClassMetadata(CartItem::class));
    }

    /** @return CartItem[] */
    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        $limit = null,
        $offset = null
    ): array {
        /** @var CartItem[] $result */
        $result = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $result;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?CartItem
    {
        /** @var CartItem|null $result */
        $result = parent::findOneBy($criteria, $orderBy);
        return $result;
    }
}
