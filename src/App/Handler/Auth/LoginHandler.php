<?php

declare(strict_types=1);

namespace App\Handler\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function password_verify;

class LoginHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $renderer,
        private EntityManager $entityManager
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            return $this->handleLogin($request);
        }

        return $this->showLoginForm();
    }

    private function showLoginForm(): ResponseInterface
    {
        return new HtmlResponse($this->renderer->render('app::auth/login', [
            'error' => null,
            'email' => '',
        ]));
    }

    private function handleLogin(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $params  = $request->getParsedBody();

        $email    = $params['email'] ?? '';
        $password = $params['password'] ?? '';

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (! $user || ! password_verify($password, $user->getPassword())) {
            return new HtmlResponse($this->renderer->render('app::auth/login', [
                'error' => 'Invalid credentials. Please try again.',
                'email' => $email,
            ]));
        }

        // Store user data in session
        $session->set('user', [
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'name'  => $user->getName(),
        ]);

        return new RedirectResponse('/my-account');
    }
}
