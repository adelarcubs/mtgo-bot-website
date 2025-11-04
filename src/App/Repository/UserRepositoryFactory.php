<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class UserRepositoryFactory
{
    public function __invoke(ContainerInterface $container): UserRepository
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        return new UserRepository($entityManager);
    }
}
