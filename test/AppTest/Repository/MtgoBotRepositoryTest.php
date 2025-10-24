<?php

declare(strict_types=1);

namespace AppTest\Repository;

use App\Entity\MtgoBot;
use App\Repository\MtgoBotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

class MtgoBotRepositoryTest extends TestCase
{
    private $entityManager;
    private MtgoBotRepository $repository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $classMetadata       = $this->createMock(ClassMetadata::class);

        $this->entityManager->method('getClassMetadata')
            ->with(MtgoBot::class)
            ->willReturn($classMetadata);

        $this->repository = new MtgoBotRepository($this->entityManager);
    }

    public function testFindActiveBots(): void
    {
        $activeBot1 = new MtgoBot('Bot 1');
        $activeBot1->setIsActive(true);

        $activeBot2 = new MtgoBot('Bot 2');
        $activeBot2->setIsActive(true);

        $inactiveBot = new MtgoBot('Inactive Bot');
        $inactiveBot->setIsActive(false);

        // Mock the parent class's findBy method
        $repositoryMock = $this->getMockBuilder(MtgoBotRepository::class)
            ->setConstructorArgs([$this->entityManager])
            ->onlyMethods(['findBy'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findBy')
            ->with(
                ['isActive' => true],
                ['name' => 'ASC']
            )
            ->willReturn([$activeBot1, $activeBot2]);

        $result = $repositoryMock->findActiveBots();

        $this->assertCount(2, $result);
        $this->assertSame('Bot 1', $result[0]->getName());
        $this->assertSame('Bot 2', $result[1]->getName());
    }

    public function testSave(): void
    {
        $bot = new MtgoBot('Test Bot');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($bot);

        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->repository->save($bot);
    }

    public function testSaveWithFlush(): void
    {
        $bot = new MtgoBot('Test Bot');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($bot);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->repository->save($bot, true);
    }

    public function testRemove(): void
    {
        $bot = new MtgoBot('Test Bot');

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($bot);

        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->repository->remove($bot);
    }

    public function testRemoveWithFlush(): void
    {
        $bot = new MtgoBot('Test Bot');

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($bot);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->repository->remove($bot, true);
    }
}
