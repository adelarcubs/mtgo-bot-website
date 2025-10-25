<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class CardSetRepositoryFactory
{
    public function __invoke(ContainerInterface $container): CardSetRepository
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        return new CardSetRepository($entityManager);
    }
}
