<?php

declare(strict_types=1);

namespace App\Handler;

use App\Repository\UserCollectionItemRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class MyCollectionHandlerFactory
{
    public function __invoke(ContainerInterface $container): MyCollectionHandler
    {
        $config = $container->get('config');

        return new MyCollectionHandler(
            $container->get(UserCollectionItemRepository::class),
            $container->get(UserRepository::class),
            $container->get(TemplateRendererInterface::class),
            $config['upload_path'] ?? 'data/uploads',
            $container->get(EntityManagerInterface::class)
        );
    }
}
