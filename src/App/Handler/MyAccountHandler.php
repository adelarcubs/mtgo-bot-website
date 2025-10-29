<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;
use App\Repository\UserRepository;
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
    private UserRepository $userRepository;

    public function __construct(
        TemplateRendererInterface $renderer,
        UserRepository $userRepository,
        string $loginUrl = '/login'
    ) {
        $this->renderer       = $renderer;
        $this->userRepository = $userRepository;
        $this->loginUrl       = $loginUrl;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $user    = $session->get('user');

        // If user is not authenticated, redirect to login
        if (! $user) {
            return new RedirectResponse($this->loginUrl);
        }

        // Get user from repository using id from session
        $userEntity = $this->userRepository->find($user['id']);

        if (! $userEntity instanceof User) {
            // If user not found in database, clear session and redirect to login
            $session->clear();
            return new RedirectResponse($this->loginUrl);
        }

        // Prepare user data from the User entity
        $userData = [
            'identity' => $userEntity->getId(),
            'roles'    => $user['roles'] ?? [], // Still using roles from session if needed
            'details'  => [
                'email'           => $userEntity->getEmail(),
                'name'            => $userEntity->getName(),
                'defaultLocation' => $userEntity->getDefaultLocation(),
                'memberSince'     => $userEntity->getCreatedAt()->format('M d, Y'),
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
