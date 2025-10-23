<?php

declare(strict_types=1);

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Roave\PsrContainerDoctrine\EntityManagerFactory;

return [
    'dependencies' => [
        'factories' => [
            'doctrine.entity_manager.orm_default'
                => EntityManagerFactory::class,
        ],
    ],
    'doctrine'     => [
        'driver' => [
            'orm_default' => [
                'class'   => MappingDriverChain::class,
                'drivers' => ['App\Entity' => 'app_entity'],
            ],
            'app_entity'  => [
                'class' => AttributeDriver::class,
                'paths' => [__DIR__ . '/../../src/App/Entity'],
            ],
        ],
    ],
];
