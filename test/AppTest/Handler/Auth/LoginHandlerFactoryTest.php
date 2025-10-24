<?php

declare(strict_types=1);

namespace AppTest\Handler\Auth;

use App\Handler\Auth\LoginHandler;
use App\Handler\Auth\LoginHandlerFactory;
use Doctrine\ORM\EntityManager;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class LoginHandlerFactoryTest extends TestCase
{
    /** @var ContainerInterface|MockObject */
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    public function testFactoryCreatesHandlerWithDependencies(): void
    {
        $renderer      = $this->createMock(TemplateRendererInterface::class);
        $entityManager = $this->createMock(EntityManager::class);

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                [TemplateRendererInterface::class, $renderer],
                [EntityManager::class, $entityManager],
            ]);

        $factory = new LoginHandlerFactory();
        $handler = $factory($this->container);

        $this->assertInstanceOf(LoginHandler::class, $handler);
    }
}
