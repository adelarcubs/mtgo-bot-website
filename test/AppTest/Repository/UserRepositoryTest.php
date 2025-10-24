<?php

declare(strict_types=1);

namespace AppTest\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private $entityManager;
    private UserRepository $repository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $classMetadata       = $this->createMock(ClassMetadata::class);

        $this->entityManager->method('getClassMetadata')
            ->with(User::class)
            ->willReturn($classMetadata);

        $this->repository = new UserRepository($this->entityManager);
    }

    public function testFindOneByEmail(): void
    {
        $user = new User('test@example.com', 'Test User', 'hashed_password');

        // Mock the parent class's findOneBy method
        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->setConstructorArgs([$this->entityManager])
            ->onlyMethods(['findOneBy'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@example.com'])
            ->willReturn($user);

        $result = $repositoryMock->findOneByEmail('test@example.com');

        $this->assertSame($user, $result);
        $this->assertSame('test@example.com', $result->getEmail());
    }

    public function testFindOneByEmailWhenUserDoesNotExist(): void
    {
        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->setConstructorArgs([$this->entityManager])
            ->onlyMethods(['findOneBy'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'nonexistent@example.com'])
            ->willReturn(null);

        $result = $repositoryMock->findOneByEmail('nonexistent@example.com');

        $this->assertNull($result);
    }

    public function testSave(): void
    {
        $user = new User('test@example.com', 'Test User', 'hashed_password');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($user);

        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->repository->save($user);
    }

    public function testSaveWithFlush(): void
    {
        $user = new User('test@example.com', 'Test User', 'hashed_password');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($user);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->repository->save($user, true);
    }

    public function testRemove(): void
    {
        $user = new User('test@example.com', 'Test User', 'hashed_password');

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($user);

        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->repository->remove($user);
    }

    public function testRemoveWithFlush(): void
    {
        $user = new User('test@example.com', 'Test User', 'hashed_password');

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($user);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->repository->remove($user, true);
    }
}
