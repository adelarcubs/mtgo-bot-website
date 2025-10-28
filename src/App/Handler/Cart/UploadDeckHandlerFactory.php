<?php

declare(strict_types=1);

namespace App\Handler\Cart;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class UploadDeckHandlerFactory
{
    public function __invoke(ContainerInterface $container): UploadDeckHandler
    {
        return new UploadDeckHandler(
            $container->get(TemplateRendererInterface::class)
        );
    }
}
