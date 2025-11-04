<?php

declare(strict_types=1);

namespace App\Factory\Admin;

use App\Handler\Admin\ListCardSetsHandler;
use App\Repository\CardSetRepository;
use Psr\Container\ContainerInterface;
use Mezzio\Template\TemplateRendererInterface;

class ListCardSetsHandlerFactory
{
    public function __invoke(ContainerInterface $container): ListCardSetsHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        $cardSetRepository = $container->get(CardSetRepository::class);
        
        return new ListCardSetsHandler($template, $cardSetRepository);
    }
}
