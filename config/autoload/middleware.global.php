<?php

declare(strict_types=1);

use App\Middleware\TemplateDataMiddleware;
use Mezzio\Template\TemplateRendererInterface;

return [
    'dependencies' => [
        'factories' => [
            TemplateDataMiddleware::class => function ($container) {
                return new TemplateDataMiddleware(
                    $container->get(TemplateRendererInterface::class)
                );
            },
        ],
    ],
];
