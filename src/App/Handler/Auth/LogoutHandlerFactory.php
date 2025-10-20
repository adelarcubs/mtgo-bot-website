<?php

declare(strict_types=1);

namespace App\Handler\Auth;

use Psr\Container\ContainerInterface;

class LogoutHandlerFactory
{
    public function __invoke(ContainerInterface $container): LogoutHandler
    {
        $config   = $container->get('config')['authentication'] ?? [];
        $loginUrl = $config['login_url'] ?? '/login';

        return new LogoutHandler($loginUrl);
    }
}
