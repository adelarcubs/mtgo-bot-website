<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class OrderItemRepositoryFactory
{
    public function __invoke(ContainerInterface $container): OrderItemRepository
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        return new OrderItemRepository($entityManager);
    }
}
