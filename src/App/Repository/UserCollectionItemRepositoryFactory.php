<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserCollectionItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Psr\Container\ContainerInterface;

class UserCollectionItemRepositoryFactory
{
    public function __invoke(ContainerInterface $container): UserCollectionItemRepository
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        $class         = new ClassMetadata(UserCollectionItem::class);
        return new UserCollectionItemRepository($entityManager, $class);
    }
}
