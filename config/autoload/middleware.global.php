<?php

declare(strict_types=1);

use App\Middleware\TemplateDataMiddleware;
use Laminas\I18n\Translator\Translator;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

return [
    'dependencies' => [
        'factories' => [
            TemplateDataMiddleware::class => function (ContainerInterface $container) {
                return new TemplateDataMiddleware(
                    $container->get(TemplateRendererInterface::class),
                    $container->get(Translator::class),
                    $container
                );
            },
        ],
    ],
];
