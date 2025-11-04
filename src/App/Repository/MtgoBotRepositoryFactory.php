<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class MtgoBotRepositoryFactory
{
    public function __invoke(ContainerInterface $container): MtgoBotRepository
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        return new MtgoBotRepository($entityManager);
    }
}
