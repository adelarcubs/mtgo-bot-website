<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Psr\Container\ContainerInterface;

class UserCollectionRepositoryFactory
{
    public function __invoke(ContainerInterface $container): UserCollectionRepository
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        $class         = new ClassMetadata(UserCollection::class);
        return new UserCollectionRepository($entityManager, $class);
    }
}
