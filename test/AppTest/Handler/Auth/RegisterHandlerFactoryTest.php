<?php

declare(strict_types=1);

namespace AppTest\Handler\Auth;

use App\Handler\Auth\RegisterHandler;
use App\Handler\Auth\RegisterHandlerFactory;
use App\Repository\UserRepository;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class RegisterHandlerFactoryTest extends TestCase
{
    /** @var ContainerInterface|MockObject */
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    public function testFactoryCreatesHandlerWithDependencies(): void
    {
        $renderer       = $this->createMock(TemplateRendererInterface::class);
        $userRepository = $this->createMock(UserRepository::class);

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                [TemplateRendererInterface::class, $renderer],
                [UserRepository::class, $userRepository],
            ]);

        $factory = new RegisterHandlerFactory();
        $handler = $factory($this->container);

        $this->assertInstanceOf(RegisterHandler::class, $handler);
    }
}
