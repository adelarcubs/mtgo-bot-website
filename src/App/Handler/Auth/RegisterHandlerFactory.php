<?php

declare(strict_types=1);

namespace App\Handler\Auth;

use App\Repository\UserRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class RegisterHandlerFactory
{
    public function __invoke(ContainerInterface $container): RegisterHandler
    {
        return new RegisterHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(UserRepository::class)
        );
    }
}
