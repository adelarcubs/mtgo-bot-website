<?php

declare(strict_types=1);

use App\Factory\TranslatorFactory;
use App\Handler\ApiHandler;
use App\Handler\ApiHandlerFactory;
use App\Handler\Auth\LoginHandler;
use App\Handler\Auth\LoginHandlerFactory;
use App\Handler\Auth\LogoutHandler;
use App\Handler\Auth\LogoutHandlerFactory;
use App\Handler\Auth\RegisterHandler;
use App\Handler\Auth\RegisterHandlerFactory;
use App\Handler\Cart\UploadDeckHandler;
use App\Handler\Cart\UploadDeckHandlerFactory;
use App\Handler\GetOrderHandler;
use App\Handler\GetOrderHandlerFactory;
use App\Handler\MyAccountHandler;
use App\Handler\MyAccountHandlerFactory;
use App\Handler\OurBotsHandler;
use App\Handler\OurBotsHandlerFactory;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthenticationMiddlewareFactory;
use App\Middleware\LocaleMiddleware;
use App\Middleware\LocaleMiddlewareFactory;
use App\Middleware\SessionMiddlewareFactory;
use App\Middleware\TemplateDataMiddleware;
use App\Middleware\TemplateDataMiddlewareFactory;
use App\Repository\MtgoBotRepository;
use App\Repository\MtgoBotRepositoryFactory;
use App\Repository\OrderRepository;
use App\Repository\OrderRepositoryFactory;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryFactory;
use App\Twig\TranslationExtension;
use App\Twig\TranslationExtensionFactory;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Container\ContainerInterface;

return [
    // Provides application-wide services.
    // We recommend using fully-qualified class names whenever possible as
    // service names.
    'dependencies' => [
        // Use 'aliases' to alias a service name to another service. The
        // key is the alias name, the value is the service to which it points.
        'aliases' => [
            // Fully\Qualified\ClassOrInterfaceName::class => Fully\Qualified\ClassName::class,
            // We'll handle the translator interface in the factories
            EntityManagerInterface::class => 'doctrine.entity_manager.orm_default',
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

            // Register the translator service
            TranslatorInterface::class => TranslatorFactory::class,

            // Alias for backward compatibility
            Translator::class => function (ContainerInterface $container) {
                return $container->get(TranslatorInterface::class);
            },

            // Alias for template usage
            'translator' => function (ContainerInterface $container) {
                return $container->get(TranslatorInterface::class);
            },

            // Register Twig extension for translations
            TranslationExtension::class => TranslationExtensionFactory::class,

            // Handlers
            GetOrderHandler::class          => GetOrderHandlerFactory::class,
            OurBotsHandler::class           => OurBotsHandlerFactory::class,
            UploadDeckHandler::class        => UploadDeckHandlerFactory::class,
            OurBotsHandler::class           => OurBotsHandlerFactory::class,
            AuthenticationMiddleware::class => AuthenticationMiddlewareFactory::class,
            ApiHandler::class               => ApiHandlerFactory::class,
            MyAccountHandler::class         => MyAccountHandlerFactory::class,
            LoginHandler::class             => LoginHandlerFactory::class,
            LogoutHandler::class            => LogoutHandlerFactory::class,
            RegisterHandler::class          => RegisterHandlerFactory::class,

            // Repositories
            UserRepository::class    => UserRepositoryFactory::class,
            MtgoBotRepository::class => MtgoBotRepositoryFactory::class,
            OrderRepository::class   => OrderRepositoryFactory::class,

            // Middleware
            SessionMiddleware::class      => SessionMiddlewareFactory::class,
            TemplateDataMiddleware::class => TemplateDataMiddlewareFactory::class,
            LocaleMiddleware::class       => LocaleMiddlewareFactory::class,
        ],

        // Register middleware for lazy loading
        'lazy_services' => [
            'proxies_target_dir' => 'data/cache/proxies',
            'proxies_namespace'  => 'Proxies',
            'write_proxy_files'  => false,
        ],
    ],
];
