<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class ProfileHandlerFactory
{
    public function __invoke(ContainerInterface $container): ProfileHandler
    {
        $renderer = $container->get(TemplateRendererInterface::class);
        $config   = $container->get('config')['authentication'] ?? [];
        $loginUrl = $config['login_url'] ?? '/login';

        return new ProfileHandler($renderer, $loginUrl);
    }
}
