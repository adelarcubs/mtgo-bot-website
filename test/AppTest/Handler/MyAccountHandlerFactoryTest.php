<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\MyAccountHandler;
use App\Handler\MyAccountHandlerFactory;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;

class MyAccountHandlerFactoryTest extends TestCase
{
    /** @var ContainerInterface|MockObject */
    private $container;

    /** @var TemplateRendererInterface|MockObject */
    private $renderer;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->renderer  = $this->createMock(TemplateRendererInterface::class);
    }

    public function testFactoryCreatesHandlerWithDefaultLoginUrl(): void
    {
        $this->container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                [TemplateRendererInterface::class, $this->renderer],
                ['config', []],
            ]);

        $factory = new MyAccountHandlerFactory();
        $handler = $factory($this->container);

        $this->assertInstanceOf(MyAccountHandler::class, $handler);
    }

    public function testFactoryCreatesHandlerWithCustomLoginUrl(): void
    {
        $config = [
            'authentication' => [
                'login_url' => '/custom-login',
            ],
        ];

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                [TemplateRendererInterface::class, $this->renderer],
                ['config', $config],
            ]);

        $factory = new MyAccountHandlerFactory();
        $handler = $factory($this->container);

        $this->assertInstanceOf(MyAccountHandler::class, $handler);
    }

    public function testFactoryThrowsExceptionWhenRendererNotAvailable(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('TemplateRendererInterface service is not available');

        $this->container->expects($this->once())
            ->method('get')
            ->with(TemplateRendererInterface::class)
            ->will($this->throwException(new RuntimeException('TemplateRendererInterface service is not available')));

        $factory = new MyAccountHandlerFactory();
        $factory($this->container);
    }
}
