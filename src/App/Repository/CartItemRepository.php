<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;

class CartItemRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /** @return CartItem[] */
    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('ci')
            ->from(CartItem::class, 'ci');

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere("ci.$field = :$field")
                ->setParameter($field, $value);
        }

        if ($orderBy !== null) {
            foreach ($orderBy as $field => $order) {
                $queryBuilder->addOrderBy("ci.$field", $order);
            }
        }

        if ($limit !== null) {
            $queryBuilder->setMaxResults($limit);
        }

        if ($offset !== null) {
            $queryBuilder->setFirstResult($offset);
        }

        /** @var CartItem[] $result */
        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?CartItem
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('ci')
            ->from(CartItem::class, 'ci')
            ->setMaxResults(1);

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere("ci.$field = :$field")
                ->setParameter($field, $value);
        }

        if ($orderBy !== null) {
            foreach ($orderBy as $field => $order) {
                $queryBuilder->addOrderBy("ci.$field", $order);
            }
        }

        /** @var CartItem|null $result */
        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return $result;
    }
}
