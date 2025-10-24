<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class MtgoBotRepositoryFactory
{
    public function __invoke(ContainerInterface $container): MtgoBotRepository
    {
        $entityManager = $container->get(EntityManager::class);
        return new MtgoBotRepository($entityManager);
    }
}
