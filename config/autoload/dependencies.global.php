<?php

declare(strict_types=1);

use App\Handler\ApiHandler;
use App\Handler\ApiHandlerFactory;
use App\Handler\Auth\LogoutHandler;
use App\Handler\Auth\LogoutHandlerFactory;
use App\Handler\OurBotsHandler;
use App\Handler\OurBotsHandlerFactory;
use App\Handler\ProfileHandler;
use App\Handler\ProfileHandlerFactory;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthenticationMiddlewareFactory;
use App\Middleware\SessionMiddlewareFactory;
use App\Repository\MtgoBotRepository;
use App\Repository\MtgoBotRepositoryFactory;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryFactory;
use Mezzio\Session\SessionMiddleware;

return [
    // Provides application-wide services.
    // We recommend using fully-qualified class names whenever possible as
    // service names.
    'dependencies' => [
        // Use 'aliases' to alias a service name to another service. The
        // key is the alias name, the value is the service to which it points.
        'aliases' => [
            // Fully\Qualified\ClassOrInterfaceName::class => Fully\Qualified\ClassName::class,
        ],
        // Use 'invokables' for constructor-less services, or services that do
        // not require arguments to the constructor. Map a service name to the
        // class name.
        'invokables' => [
            // Fully\Qualified\InterfaceName::class => Fully\Qualified\ClassName::class,
        ],
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories' => [
            // Fully\Qualified\ClassName::class => Fully\Qualified\FactoryName::class,
            OurBotsHandler::class           => OurBotsHandlerFactory::class,
            AuthenticationMiddleware::class => AuthenticationMiddlewareFactory::class,
            ApiHandler::class               => ApiHandlerFactory::class,
            ProfileHandler::class           => ProfileHandlerFactory::class,
            LogoutHandler::class            => LogoutHandlerFactory::class,
            SessionMiddleware::class        => SessionMiddlewareFactory::class,
            UserRepository::class           => UserRepositoryFactory::class,
            MtgoBotRepository::class        => MtgoBotRepositoryFactory::class,
        ],

        // Register middleware for lazy loading
        'lazy_services' => [
            'proxies_target_dir' => 'data/cache/proxies',
            'proxies_namespace'  => 'Proxies',
            'write_proxy_files'  => false,
        ],
    ],
];
