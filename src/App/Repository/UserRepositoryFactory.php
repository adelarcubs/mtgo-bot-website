<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class UserRepositoryFactory
{
    public function __invoke(ContainerInterface $container): UserRepository
    {
        $entityManager = $container->get(EntityManager::class);
        return new UserRepository($entityManager);
    }
}
