<?php

declare(strict_types=1);

namespace AppTest\Middleware;

use App\Middleware\LocaleMiddleware;
use App\Middleware\LocaleMiddlewareFactory;
use Laminas\I18n\Translator\Translator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class LocaleMiddlewareFactoryTest extends TestCase
{
    /** @var ContainerInterface&MockObject */
    private $container;

    /** @var Translator&MockObject */
    private $translator;

    private LocaleMiddlewareFactory $factory;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(Translator::class);
        $this->container  = $this->createMock(ContainerInterface::class);
        $this->factory    = new LocaleMiddlewareFactory();
    }

    public function testInvokeReturnsLocaleMiddleware(): void
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with(Translator::class)
            ->willReturn($this->translator);

        $middleware = ($this->factory)($this->container);

        $this->assertInstanceOf(LocaleMiddleware::class, $middleware);
    }
}
