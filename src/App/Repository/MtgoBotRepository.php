<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MtgoBot;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @method MtgoBot|null find($id, $lockMode = null, $lockVersion = null)
 * @method MtgoBot|null findOneBy(array $criteria, array $orderBy = null)
 * @method MtgoBot[]    findAll()
 * @method MtgoBot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MtgoBotRepository extends EntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($entityManager, $entityManager->getClassMetadata(MtgoBot::class));
    }

    public function findActiveBots(): array
    {
        return $this->findBy(['isActive' => true], ['name' => 'ASC']);
    }

    public function save(MtgoBot $entity, bool $flush = false): void
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(MtgoBot $entity, bool $flush = false): void
    {
        $this->entityManager->remove($entity);

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
