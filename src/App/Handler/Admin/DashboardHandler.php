<?php

declare(strict_types=1);

namespace App\Handler\Admin;

use Laminas\Diactoros\Response;
use Mezzio\Router;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DashboardHandler implements RequestHandlerInterface
{
    /** @var TemplateRendererInterface */
    private $renderer;

    /** @var Router\RouterInterface */
    private $router;

    public function __construct(
        TemplateRendererInterface $renderer,
        Router\RouterInterface $router
    ) {
        $this->renderer = $renderer;
        $this->router   = $router;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Check if user is admin (you should implement proper authorization)
        // $user = $request->getAttribute('user');
        // if (!$user || !$user->isAdmin()) {
        //     return new RedirectResponse('/login');
        // }

        // Add any data you want to pass to the template
        $data = [
            'current_user' => [
                'username' => 'Admin',
                'email'    => 'admin@example.com',
                'role'     => 'admin',
            ],
            'router'       => $this->router,
        ];

        $response = new Response();
        $response->getBody()->write(
            $this->renderer->render('app::admin/dashboard', $data)
        );

        return $response;
    }

    /**
     * This method is kept for backward compatibility but should be removed in the future
     * as we're now using the factory class for dependency injection
     *
     * @deprecated Use DashboardHandlerFactory instead
     */
    public static function factory(ContainerInterface $container): self
    {
        $factory = new DashboardHandlerFactory();
        return $factory($container);
    }
}
