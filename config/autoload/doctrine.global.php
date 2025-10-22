<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;

return [
    'dependencies' => [
        'factories' => [
            'doctrine.entity_manager.orm_default' => function (ContainerInterface $container) {
                $config         = $container->get('config');
                $doctrineConfig = $config['doctrine'];

                // Setup Doctrine
                $doctrine = Setup::createAttributeMetadataConfiguration(
                    $doctrineConfig['entity_paths'],
                    $doctrineConfig['dev_mode'] ?? false,
                    $doctrineConfig['proxy_dir'],
                    $doctrineConfig['cache']
                );

                // Get database connection configuration
                $connection = $config['database'];
                return EntityManager::create($connection, $doctrine);
            },
        ],
    ],
    'doctrine'     => [
        'entity_paths'                 => [
            __DIR__ . '/../src/App/Entity',
        ],
        'cache_dir'                    => 'data/cache/doctrine',
        'proxy_dir'                    => 'data/doctrine/proxy',
        'cache'                        => null,
        'use_simple_annotation_reader' => false,
    ],
];
