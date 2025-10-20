<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OurBotsHandler implements RequestHandlerInterface
{
    /** @var TemplateRendererInterface */
    private $renderer;

    public function __construct(TemplateRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // You can add data to pass to the template here
        $data = [
            'bots' => [
                [
                    'name'        => 'MTGOTrader Bot',
                    'description' => 'Our main trading bot with high liquidity',
                    'status'      => 'Online',
                    'uptime'      => '99.9%',
                ],
                [
                    'name'        => 'CardHoarder Bot',
                    'description' => 'Specialized in bulk trades and collections',
                    'status'      => 'Online',
                    'uptime'      => '99.8%',
                ],
                [
                    'name'        => 'GoatBots',
                    'description' => 'Fast and reliable bot for competitive trading',
                    'status'      => 'Online',
                    'uptime'      => '99.7%',
                ],
            ],
        ];

        return new HtmlResponse($this->renderer->render(
            'app::our-bots',
            $data
        ));
    }
}
