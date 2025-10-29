<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class CartItemRepositoryFactory
{
    public function __invoke(ContainerInterface $container): CartItemRepository
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);

        return new CartItemRepository($entityManager);
    }
}
