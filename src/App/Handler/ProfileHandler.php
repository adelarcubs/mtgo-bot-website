<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Authentication\UserInterface;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;

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
        if (!$user instanceof UserInterface) {
            return new RedirectResponse($this->loginUrl);
        }

        // Get user data from the session
        $userData = [
            'identity' => $user->getIdentity(),
            'roles' => $user->getRoles(),
            'details' => $user->getDetails()
        ];

        return new HtmlResponse(
            $this->renderer->render('app::profile', [
                'user' => $userData,
                'layout' => 'layout::default',
            ])
        );
    }
}
