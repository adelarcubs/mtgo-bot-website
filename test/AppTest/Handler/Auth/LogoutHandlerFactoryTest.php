<?php

declare(strict_types=1);

namespace AppTest\Handler\Auth;

use App\Handler\Auth\LogoutHandler;
use App\Handler\Auth\LogoutHandlerFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class LogoutHandlerFactoryTest extends TestCase
{
    /** @var ContainerInterface|MockObject */
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    public function testFactoryCreatesHandlerWithDefaultLoginUrl(): void
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn(['authentication' => []]);

        $factory = new LogoutHandlerFactory();
        $handler = $factory($this->container);

        $this->assertInstanceOf(LogoutHandler::class, $handler);
    }

    public function testFactoryCreatesHandlerWithConfiguredLoginUrl(): void
    {
        $config = [
            'authentication' => [
                'login_url' => '/custom-login',
            ],
        ];

        $this->container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new LogoutHandlerFactory();
        $handler = $factory($this->container);

        $this->assertInstanceOf(LogoutHandler::class, $handler);
    }
}
