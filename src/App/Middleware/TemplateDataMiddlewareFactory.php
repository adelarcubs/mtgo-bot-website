<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\I18n\Translator\Translator;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class TemplateDataMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): TemplateDataMiddleware
    {
        return new TemplateDataMiddleware(
            $container->get(TemplateRendererInterface::class),
            $container->get(Translator::class),
            $container
        );
    }
}
