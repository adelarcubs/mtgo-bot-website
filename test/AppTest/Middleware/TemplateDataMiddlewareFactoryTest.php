<?php

declare(strict_types=1);

namespace AppTest\Middleware;

use App\Middleware\TemplateDataMiddleware;
use App\Middleware\TemplateDataMiddlewareFactory;
use Laminas\I18n\Translator\Translator;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class TemplateDataMiddlewareFactoryTest extends TestCase
{
    private ContainerInterface|MockObject $container;
    private TemplateDataMiddlewareFactory $factory;
    private TemplateRendererInterface|MockObject $renderer;
    private Translator|MockObject $translator;

    protected function setUp(): void
    {
        $this->container  = $this->createMock(ContainerInterface::class);
        $this->renderer   = $this->createMock(TemplateRendererInterface::class);
        $this->translator = $this->createMock(Translator::class);
        $this->factory    = new TemplateDataMiddlewareFactory();
    }

    public function testInvokeReturnsTemplateDataMiddleware(): void
    {
        $this->container->method('get')
            ->willReturnMap([
                [TemplateRendererInterface::class, $this->renderer],
                [Translator::class, $this->translator],
            ]);

        $middleware = ($this->factory)($this->container);
        $this->assertInstanceOf(TemplateDataMiddleware::class, $middleware);
    }

    public function testDependenciesAreRetrievedFromContainer(): void
    {
        $this->container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                [TemplateRendererInterface::class, $this->renderer],
                [Translator::class, $this->translator],
            ]);

        $middleware = ($this->factory)($this->container);
        $this->assertInstanceOf(TemplateDataMiddleware::class, $middleware);
    }
}
