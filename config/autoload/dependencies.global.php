<?php

declare(strict_types=1);

use App\Client\MtgJsonClient;
use App\Client\MtgJsonClientInterface;
use App\Factory\Admin\ImportCardSetsHandlerFactory;
use App\Factory\Admin\ListCardSetsHandlerFactory;
use App\Factory\MtgJsonClientFactory;
use App\Factory\TranslatorFactory;
use App\Handler\Admin\DashboardHandler;
use App\Handler\Admin\DashboardHandlerFactory;
use App\Handler\Admin\Factory\ImportCardsFromSetHandlerFactory;
use App\Handler\Admin\ImportCardSetsHandler;
use App\Handler\Admin\ImportCardsFromSetHandler;
use App\Handler\Admin\ListCardSetsHandler;
use App\Handler\ApiHandler;
use App\Handler\ApiHandlerFactory;
use App\Handler\Auth\LoginHandler;
use App\Handler\Auth\LoginHandlerFactory;
use App\Handler\Auth\LogoutHandler;
use App\Handler\Auth\LogoutHandlerFactory;
use App\Handler\Auth\RegisterHandler;
use App\Handler\Auth\RegisterHandlerFactory;
use App\Handler\Cart\GetCartHandler;
use App\Handler\Cart\GetCartHandlerFactory;
use App\Handler\Cart\UploadDeckHandler;
use App\Handler\Cart\UploadDeckHandlerFactory;
use App\Handler\GetOrderHandler;
use App\Handler\GetOrderHandlerFactory;
use App\Handler\MyAccountHandler;
use App\Handler\MyAccountHandlerFactory;
use App\Handler\MyCollection\MyCollectionHandler;
use App\Handler\MyCollection\MyCollectionHandlerFactory;
use App\Handler\MyCollection\UploadCollectionHandler;
use App\Handler\MyCollection\UploadCollectionHandlerFactory;
use App\Handler\MyRentHandler;
use App\Handler\MyRentHandlerFactory;
use App\Handler\OurBotsHandler;
use App\Handler\OurBotsHandlerFactory;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthenticationMiddlewareFactory;
use App\Middleware\LocaleMiddleware;
use App\Middleware\LocaleMiddlewareFactory;
use App\Middleware\SessionMiddlewareFactory;
use App\Middleware\TemplateDataMiddleware;
use App\Middleware\TemplateDataMiddlewareFactory;
use App\Repository\CardSetRepository;
use App\Repository\CardSetRepositoryFactory;
use App\Repository\CartRepository;
use App\Repository\CartRepositoryFactory;
use App\Repository\MtgoBotRepository;
use App\Repository\MtgoBotRepositoryFactory;
use App\Repository\MtgoItemRepository;
use App\Repository\MtgoItemRepositoryFactory;
use App\Repository\OrderRepository;
use App\Repository\OrderRepositoryFactory;
use App\Repository\RentedCardRepository;
use App\Repository\RentedCardRepositoryFactory;
use App\Repository\UserCollectionItemRepository;
use App\Repository\UserCollectionItemRepositoryFactory;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryFactory;
use App\Service\DekFileReader;
use App\Twig\TranslationExtension;
use App\Twig\TranslationExtensionFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Container\ContainerInterface;

return [
    // Application configuration
    'upload_path' => 'data/uploads',

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
            EntityManager::class          => 'doctrine.entity_manager.orm_default',
        ],
        // Use 'invokables' for constructor-less services, or services that do
        // not require arguments to the constructor. Map a service name to the
        // class name.
        'invokables' => [
            Handler\PingHandler::class => Handler\PingHandler::class,
            DekFileReader::class       => DekFileReader::class,
        ],
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories' => [
        // Admin handlers
            DashboardHandler::class      => DashboardHandlerFactory::class,
            ImportCardSetsHandler::class => ImportCardSetsHandlerFactory::class,
            // Repositories
            RentedCardRepository::class => RentedCardRepositoryFactory::class,

        // Handlers
            MyRentHandler::class => MyRentHandlerFactory::class,
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
            ImportCardsFromSetHandler::class => ImportCardsFromSetHandlerFactory::class,
            ListCardSetsHandler::class       => ListCardSetsHandlerFactory::class,
            GetOrderHandler::class           => GetOrderHandlerFactory::class,
            OurBotsHandler::class            => OurBotsHandlerFactory::class,
            UploadDeckHandler::class         => UploadDeckHandlerFactory::class,
            GetCartHandler::class            => GetCartHandlerFactory::class,
            OurBotsHandler::class            => OurBotsHandlerFactory::class,
            MyCollectionHandler::class       => MyCollectionHandlerFactory::class,
            UploadCollectionHandler::class   => UploadCollectionHandlerFactory::class,
            AuthenticationMiddleware::class  => AuthenticationMiddlewareFactory::class,
            ApiHandler::class                => ApiHandlerFactory::class,
            MyAccountHandler::class          => MyAccountHandlerFactory::class,
            LoginHandler::class              => LoginHandlerFactory::class,
            LogoutHandler::class             => LogoutHandlerFactory::class,
            RegisterHandler::class           => RegisterHandlerFactory::class,

            // MTGJSON Client
            MtgJsonClient::class          => MtgJsonClientFactory::class,
            MtgJsonClientInterface::class => MtgJsonClientFactory::class,

            // Repositories
            UserRepository::class               => UserRepositoryFactory::class,
            CardSetRepository::class            => CardSetRepositoryFactory::class,
            MtgoBotRepository::class            => MtgoBotRepositoryFactory::class,
            MtgoItemRepository::class           => MtgoItemRepositoryFactory::class,
            OrderRepository::class              => OrderRepositoryFactory::class,
            CartRepository::class               => CartRepositoryFactory::class,
            RentedCardRepository::class         => RentedCardRepositoryFactory::class,
            UserCollectionItemRepository::class => UserCollectionItemRepositoryFactory::class,

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
