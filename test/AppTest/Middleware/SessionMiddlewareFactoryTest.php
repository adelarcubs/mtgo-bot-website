<?php

declare(strict_types=1);

namespace AppTest\Middleware;

use App\Middleware\SessionMiddlewareFactory;
use Mezzio\Session\SessionMiddleware as MezzioSessionMiddleware;
use Mezzio\Session\SessionPersistenceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;

class SessionMiddlewareFactoryTest extends TestCase
{
    private ContainerInterface|MockObject $container;
    private SessionPersistenceInterface|MockObject $sessionPersistence;
    private SessionMiddlewareFactory $factory;

    protected function setUp(): void
    {
        $this->container          = $this->createMock(ContainerInterface::class);
        $this->sessionPersistence = $this->createMock(SessionPersistenceInterface::class);
        $this->factory            = new SessionMiddlewareFactory();
    }

    public function testInvokeReturnsSessionMiddleware(): void
    {
        // Configure the container to return our mock session persistence
        $this->container->expects($this->once())
            ->method('get')
            ->with(SessionPersistenceInterface::class)
            ->willReturn($this->sessionPersistence);

        // Execute the factory
        $middleware = ($this->factory)($this->container);

        // Assert that we get back a SessionMiddleware instance
        $this->assertInstanceOf(MezzioSessionMiddleware::class, $middleware);
    }

    public function testInvokeThrowsExceptionWhenPersistenceNotInContainer(): void
    {
        // Configure the container to throw an exception when SessionPersistenceInterface is requested
        $this->container->expects($this->once())
            ->method('get')
            ->with(SessionPersistenceInterface::class)
            ->willThrowException(new RuntimeException('Service not found'));

        // Expect an exception to be thrown
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Service not found');

        // Execute the factory
        ($this->factory)($this->container);
    }
}
