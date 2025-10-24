<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\I18n\Translator\Translator;
use Psr\Container\ContainerInterface;

class LocaleMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): LocaleMiddleware
    {
        $translator = $container->get(Translator::class);
        return new LocaleMiddleware($translator, $container);
    }
}
