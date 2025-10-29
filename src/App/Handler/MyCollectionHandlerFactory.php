<?php

declare(strict_types=1);

namespace App\Handler;

use App\Repository\UserCollectionRepository;
use App\Repository\UserRepository;
use Psr\Container\ContainerInterface;

class MyCollectionHandlerFactory
{
    public function __invoke(ContainerInterface $container): MyCollectionHandler
    {
        $config = $container->get('config');
        
        return new MyCollectionHandler(
            $container->get(UserCollectionRepository::class),
            $container->get(UserRepository::class),
            $container->get('Mezzio\\Template\\TemplateRendererInterface'),
            $config['upload_path'] ?? 'data/uploads'
        );
    }
}
