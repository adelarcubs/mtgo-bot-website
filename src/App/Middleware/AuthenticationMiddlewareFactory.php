<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;

class AuthenticationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): AuthenticationMiddleware
    {
        $config = $container->get('config')['authentication'] ?? [];
        return new AuthenticationMiddleware($config);
    }
}
