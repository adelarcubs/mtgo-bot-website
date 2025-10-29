<?php

declare(strict_types=1);

namespace App\Repository;

use Psr\Container\ContainerInterface;

class CartItemRepositoryFactory
{
    public function __invoke(ContainerInterface $container): CartItemRepository
    {
        return new CartItemRepository(
            $container->get('doctrine')->getManager()
        );
    }
}
