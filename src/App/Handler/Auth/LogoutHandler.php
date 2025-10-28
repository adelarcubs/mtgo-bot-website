<?php

declare(strict_types=1);

namespace App\Handler\Auth;

use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LogoutHandler implements RequestHandlerInterface
{
    private string $loginUrl;

    public function __construct(string $loginUrl = '/login')
    {
        $this->loginUrl = $loginUrl;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $locale = $session->get('locale');

        if ($session instanceof SessionInterface) {
            // Clear all session data
            $session->clear();
            // Regenerate session ID to prevent session fixation
            $session->regenerate();
        }

        $session->set('locale', $locale);

        return new RedirectResponse($this->loginUrl);
    }
}
