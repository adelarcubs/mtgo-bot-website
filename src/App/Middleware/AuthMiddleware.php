<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouteResult;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $request->getAttribute('user');
        
        if (!$user) {
            $routeResult = $request->getAttribute(RouteResult::class);
            $redirectUrl = $routeResult ? $routeResult->getMatchedRouteName() : 'home';
            
            return new RedirectResponse(
                $request->getAttribute('router')->generateUri('auth.login', [
                    'redirect' => $redirectUrl,
                ])
            );
        }
        
        return $handler->handle($request);
    }
}
