<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MyAccountHandler implements RequestHandlerInterface
{
    private TemplateRendererInterface $renderer;
    private string $loginUrl;

    public function __construct(TemplateRendererInterface $renderer, string $loginUrl = '/login')
    {
        $this->renderer = $renderer;
        $this->loginUrl = $loginUrl;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $user    = $session->get('user');

        // If user is not authenticated, redirect to login
        if (! $user) {
            return new RedirectResponse($this->loginUrl);
        }

        // Get user data from the session
        $userData = [
            'identity' => $user['id'],
            'roles'    => $user['roles'] ?? [],
            'details'  => [
                'email' => $user['email'],
                'name'  => $user['name'] ?? '',
            ],
        ];

        return new HtmlResponse(
            $this->renderer->render('app::my-account', [
                'user'   => $userData,
                'layout' => 'layout::default',
            ])
        );
    }
}
