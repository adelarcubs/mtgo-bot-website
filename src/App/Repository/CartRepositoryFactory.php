<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class CartRepositoryFactory
{
    public function __invoke(ContainerInterface $container): CartRepository
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        $metadata      = $entityManager->getClassMetadata(Cart::class);

        return new CartRepository($entityManager, $metadata);
    }
}
