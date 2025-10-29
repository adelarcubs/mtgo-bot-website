<?php

declare(strict_types=1);

namespace App\Repository;

use Psr\Container\ContainerInterface;

class OrderItemRepositoryFactory
{
    public function __invoke(ContainerInterface $container): OrderItemRepository
    {
        return new OrderItemRepository(
            $container->get('doctrine')->getManager()
        );
    }
}
