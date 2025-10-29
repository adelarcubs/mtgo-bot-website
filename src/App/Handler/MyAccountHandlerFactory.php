<?php

declare(strict_types=1);

namespace App\Handler;

use App\Repository\UserRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class MyAccountHandlerFactory
{
    public function __invoke(ContainerInterface $container): MyAccountHandler
    {
        $renderer       = $container->get(TemplateRendererInterface::class);
        $userRepository = $container->get(UserRepository::class);
        $config         = $container->get('config')['authentication'] ?? [];
        $loginUrl       = $config['login_url'] ?? '/login';

        return new MyAccountHandler($renderer, $userRepository, $loginUrl);
    }
}
