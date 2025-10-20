<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');

        // Check if Authorization header exists and starts with 'Basic '
        if (empty($authHeader) || strpos($authHeader, 'Basic ') !== 0) {
            return new JsonResponse(
                ['error' => 'Unauthorized - Missing or invalid Authorization header'],
                401,
                ['WWW-Authenticate' => 'Basic realm="' . ($this->config['realm'] ?? 'API') . '"']
            );
        }

        // Extract the token from the Authorization header
        $token = base64_decode(trim(substr($authHeader, 6)));
        
        // Check if the token exists in our configuration
        if (!isset($this->config['tokens'][$token])) {
            return new JsonResponse(
                ['error' => 'Unauthorized - Invalid token'],
                401,
                ['WWW-Authenticate' => 'Basic realm="' . ($this->config['realm'] ?? 'API') . '"']
            );
        }

        // Add token info to the request for use in subsequent middleware/controllers
        $tokenInfo = $this->config['tokens'][$token];
        $request = $request->withAttribute('token', [
            'id' => $token,
            'scopes' => $tokenInfo['scopes'] ?? [],
            'description' => $tokenInfo['description'] ?? '',
        ]);

        // Continue to the next middleware/request handler
        return $handler->handle($request);
    }
}
