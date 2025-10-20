<?php

declare(strict_types=1);

namespace App\Middleware;

use Mezzio\Session\SessionMiddleware as MezzioSessionMiddleware;
use Mezzio\Session\SessionPersistenceInterface;
use Psr\Container\ContainerInterface;

class SessionMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MezzioSessionMiddleware
    {
        return new MezzioSessionMiddleware(
            $container->get(SessionPersistenceInterface::class)
        );
    }
}
