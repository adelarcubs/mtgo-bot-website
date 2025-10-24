<?php

declare(strict_types=1);

namespace App\Handler;

use App\Repository\MtgoBotRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class OurBotsHandlerFactory
{
    public function __invoke(ContainerInterface $container): OurBotsHandler
    {
        return new OurBotsHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(MtgoBotRepository::class)
        );
    }
}
