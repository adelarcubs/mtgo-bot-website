<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class OrderRepositoryFactory
{
    public function __invoke(ContainerInterface $container): OrderRepository
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        $metadata = $entityManager->getClassMetadata(Order::class);
        
        return new OrderRepository($entityManager, $metadata);
    }
}