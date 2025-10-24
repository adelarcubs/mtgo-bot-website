<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\OurBotsHandler;
use App\Handler\OurBotsHandlerFactory;
use App\Repository\MtgoBotRepository;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class OurBotsHandlerFactoryTest extends TestCase
{
    /** @var ContainerInterface|MockObject */
    private $container;

    /** @var TemplateRendererInterface|MockObject */
    private $renderer;

    /** @var MtgoBotRepository|MockObject */
    private $botRepository;

    /** @var OurBotsHandlerFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->container     = $this->createMock(ContainerInterface::class);
        $this->renderer      = $this->createMock(TemplateRendererInterface::class);
        $this->botRepository = $this->createMock(MtgoBotRepository::class);
        $this->factory       = new OurBotsHandlerFactory();
    }

    public function testFactoryCreatesHandlerWithDependencies(): void
    {
        $this->container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                [TemplateRendererInterface::class, $this->renderer],
                [MtgoBotRepository::class, $this->botRepository],
            ]);

        $handler = ($this->factory)($this->container);

        $this->assertInstanceOf(OurBotsHandler::class, $handler);
    }
}
