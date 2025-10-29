<?php

declare(strict_types=1);

namespace App\Handler\MyCollection;

use App\Repository\UserCollectionItemRepository;
use App\Repository\UserRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class MyCollectionHandlerFactory
{
    public function __invoke(ContainerInterface $container): MyCollectionHandler
    {
        return new MyCollectionHandler(
            $container->get(UserCollectionItemRepository::class),
            $container->get(UserRepository::class),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
