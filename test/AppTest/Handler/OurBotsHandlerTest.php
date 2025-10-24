<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\OurBotsHandler;
use App\Repository\MtgoBotRepository;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OurBotsHandlerTest extends TestCase
{
    /** @var TemplateRendererInterface|MockObject */
    private $renderer;

    /** @var MtgoBotRepository|MockObject */
    private $botRepository;

    /** @var OurBotsHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->renderer      = $this->createMock(TemplateRendererInterface::class);
        $this->botRepository = $this->createMock(MtgoBotRepository::class);

        $this->handler = new OurBotsHandler(
            $this->renderer,
            $this->botRepository
        );
    }

    public function testHandleRendersTemplateWithActiveBots(): void
    {
        $activeBots = [
            ['id' => 1, 'name' => 'Bot1', 'status' => 'online'],
            ['id' => 2, 'name' => 'Bot2', 'status' => 'online'],
        ];

        $request = new ServerRequest();

        $this->botRepository->expects($this->once())
            ->method('findActiveBots')
            ->willReturn($activeBots);

        $this->renderer->expects($this->once())
            ->method('render')
            ->with('app::our-bots', ['bots' => $activeBots])
            ->willReturn('rendered content');

        $response = $this->handler->handle($request);

        $this->assertSame('rendered content', (string) $response->getBody());
        $this->assertSame(200, $response->getStatusCode());
    }
}
