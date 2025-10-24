<?php

declare(strict_types=1);

namespace App\Handler;

use App\Repository\MtgoBotRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OurBotsHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $renderer,
        private MtgoBotRepository $botRepository
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $activeBots = $this->botRepository->findActiveBots();

        return new HtmlResponse($this->renderer->render(
            'app::our-bots',
            ['bots' => $activeBots]
        ));
    }
}
