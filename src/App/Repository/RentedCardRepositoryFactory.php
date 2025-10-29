<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RentedCard;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class RentedCardRepositoryFactory
{
    public function __invoke(ContainerInterface $container): RentedCardRepository
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        $metadata      = $entityManager->getClassMetadata(RentedCard::class);

        return new RentedCardRepository($entityManager, $metadata);
    }
}
