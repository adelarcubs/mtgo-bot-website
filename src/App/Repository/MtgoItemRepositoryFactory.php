<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class MtgoItemRepositoryFactory
{
    public function __invoke(ContainerInterface $container): MtgoItemRepository
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        return new MtgoItemRepository($entityManager);
    }
}
