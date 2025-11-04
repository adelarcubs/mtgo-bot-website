<?php

declare(strict_types=1);

namespace App\Factory\Admin;

use App\Handler\Admin\ImportCardSetsHandler;
use App\Repository\CardSetRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class ImportCardSetsHandlerFactory
{
    public function __invoke(ContainerInterface $container): ImportCardSetsHandler
    {
        $template          = $container->get(TemplateRendererInterface::class);
        $cardSetRepository = $container->get(CardSetRepository::class);

        return new ImportCardSetsHandler($template, $cardSetRepository);
    }
}
