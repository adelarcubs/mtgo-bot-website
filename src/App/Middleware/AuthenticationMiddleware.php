<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function base64_decode;
use function in_array;
use function rtrim;
use function str_ends_with;
use function str_starts_with;
use function substr;
use function trim;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private array $config;
    private array $publicRoutes;

    public function __construct(array $config)
    {
        $this->config       = $config;
        $this->publicRoutes = $config['public_routes'] ?? [];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        // Check if the route is public
        if ($this->isPublicRoute($path)) {
            return $handler->handle($request);
        }

        // Check if this is an API route (starts with /api)
        if (str_starts_with($path, '/api/')) {
            return $this->handleApiAuth($request, $handler);
        }
        // Handle web session-based authentication for non-API routes
        return $this->handleWebAuth($request, $handler);
    }

    private function handleApiAuth(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');

        // Check if Authorization header exists and starts with 'Bearer ' or 'Basic '
        if (empty($authHeader) || ! (str_starts_with($authHeader, 'Bearer ') || str_starts_with($authHeader, 'Basic '))) {
            return new JsonResponse(
                ['error' => 'Unauthorized - Missing or invalid Authorization header'],
                401,
                ['WWW-Authenticate' => 'Bearer realm="' . ($this->config['realm'] ?? 'API') . '"']
            );
        }

        // Handle Bearer token
        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = trim(substr($authHeader, 7));

            // Check if the token exists in our configuration
            if (! isset($this->config['tokens'][$token])) {
                return new JsonResponse(
                    ['error' => 'Unauthorized - Invalid token'],
                    401,
                    ['WWW-Authenticate' => 'Bearer realm="' . ($this->config['realm'] ?? 'API') . '"']
                );
            }

            // Add token info to the request
            $tokenInfo = $this->config['tokens'][$token];
            $request   = $request->withAttribute('token', [
                'id'          => $token,
                'scopes'      => $tokenInfo['scopes'] ?? [],
                'description' => $tokenInfo['description'] ?? '',
            ]);
        }
        // Handle Basic Auth (for backward compatibility)
        elseif (str_starts_with($authHeader, 'Basic ')) {
            $token = base64_decode(trim(substr($authHeader, 6)));

            if (! isset($this->config['tokens'][$token])) {
                return new JsonResponse(
                    ['error' => 'Unauthorized - Invalid credentials'],
                    401,
                    ['WWW-Authenticate' => 'Basic realm="' . ($this->config['realm'] ?? 'API') . '"']
                );
            }

            $tokenInfo = $this->config['tokens'][$token];
            $request   = $request->withAttribute('token', [
                'id'          => $token,
                'scopes'      => $tokenInfo['scopes'] ?? [],
                'description' => $tokenInfo['description'] ?? '',
            ]);
        }

        return $handler->handle($request);
    }

    private function handleWebAuth(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        // Check if user is authenticated in session
        $user = $session->get('user');

        if (! $user) {
            // Store the current URL for redirect after login
            $session->set('redirect_after_login', (string) $request->getUri());

            // Redirect to login page
            return new RedirectResponse($this->config['login_url'] ?? '/login');
        }

        // Add user to the request attributes
        $request = $request->withAttribute(UserInterface::class, $user);

        return $handler->handle($request);
    }

    private function isPublicRoute(string $path): bool
    {
        // Check exact matches
        if (in_array($path, $this->publicRoutes, true)) {
            return true;
        }

        // Check path prefixes (e.g., '/assets/')
        foreach ($this->publicRoutes as $publicRoute) {
            if (
                str_ends_with($publicRoute, '/*') &&
                str_starts_with($path, rtrim($publicRoute, '*'))
            ) {
                return true;
            }
        }

        return false;
    }
}
