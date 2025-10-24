<?php

declare(strict_types=1);

namespace AppTest\Repository;

use App\Repository\UserRepository;
use App\Repository\UserRepositoryFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TypeError;

class UserRepositoryFactoryTest extends TestCase
{
    private ContainerInterface|MockObject $container;
    private EntityManager|MockObject $entityManager;
    private UserRepositoryFactory $factory;

    protected function setUp(): void
    {
        $this->container     = $this->createMock(ContainerInterface::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->factory       = new UserRepositoryFactory();
    }

    public function testInvokeReturnsUserRepositoryInstance(): void
    {
        $classMetadata = $this->createMock(ClassMetadata::class);

        $this->entityManager->method('getClassMetadata')
            ->willReturn($classMetadata);

        $this->container->expects($this->once())
            ->method('get')
            ->with(EntityManager::class)
            ->willReturn($this->entityManager);

        $result = ($this->factory)($this->container);

        $this->assertInstanceOf(UserRepository::class, $result);
    }

    public function testInvokeThrowsExceptionWhenEntityManagerNotInContainer(): void
    {
        $this->expectException(TypeError::class);

        $this->container->expects($this->once())
            ->method('get')
            ->with(EntityManager::class)
            ->willReturn(null);

        ($this->factory)($this->container);
    }
}
