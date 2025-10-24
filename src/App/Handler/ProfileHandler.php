<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProfileHandler implements RequestHandlerInterface
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
        $user = $request->getAttribute(UserInterface::class);

        // If user is not authenticated, redirect to login
        if (! $user instanceof UserInterface) {
            // return new RedirectResponse($this->loginUrl);
        }

        // Get user data from the session
        $userData = [
            'identity' => $user['id'],
            'roles'    => [],
            'details'  => $user['email'],
        ];

        return new HtmlResponse(
            $this->renderer->render('app::profile', [
                'user'   => $userData,
                'layout' => 'layout::default',
            ])
        );
    }
}
