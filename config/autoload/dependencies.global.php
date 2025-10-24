<?php

declare(strict_types=1);

use App\Handler\ApiHandler;
use App\Handler\ApiHandlerFactory;
use App\Handler\Auth\LoginHandler;
use App\Handler\Auth\LoginHandlerFactory;
use App\Handler\Auth\LogoutHandler;
use App\Handler\Auth\LogoutHandlerFactory;
use App\Handler\Auth\RegisterHandler;
use App\Handler\Auth\RegisterHandlerFactory;
use App\Handler\OurBotsHandler;
use App\Handler\OurBotsHandlerFactory;
use App\Handler\ProfileHandler;
use App\Handler\ProfileHandlerFactory;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthenticationMiddlewareFactory;
use App\Middleware\LocaleMiddleware;
use App\Middleware\LocaleMiddlewareFactory;
use App\Middleware\SessionMiddlewareFactory;
use App\Middleware\TemplateDataMiddleware;
use App\Middleware\TemplateDataMiddlewareFactory;
use App\Repository\MtgoBotRepository;
use App\Repository\MtgoBotRepositoryFactory;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryFactory;
use App\Twig\TranslationExtension;
use App\Twig\TranslationExtensionFactory;
use Laminas\I18n\Translator\Loader\Gettext;
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
            TranslatorInterface::class => function (ContainerInterface $container) {
                $config     = $container->get('config');
                $translator = new Translator();

                // Set default locale from config
                $defaultLocale = $config['i18n']['default_locale'] ?? 'pt_BR';
                // $translator->setLocale($defaultLocale);
                // $translator->setFallbackLocale($defaultLocale);

                // Enable event manager for locale change events
                $translator->enableEventManager();

                // Add translation files if configured
                if (isset($config['i18n']['translation_file_patterns'])) {
                    $loader = new Gettext();
                    $translator->getPluginManager()->setService('gettext', $loader);

                    foreach ($config['i18n']['translation_file_patterns'] as $idx => $pattern) {
                        $type       = $pattern['type'] ?? 'gettext';
                        $baseDir    = $pattern['base_dir'] ?? '';
                        $patternStr = $pattern['pattern'] ?? '%s/LC_MESSAGES/default.mo';

                        // Ensure base directory exists
                        if (! is_dir($baseDir)) {
                            error_log(sprintf(
                                'Translation base directory does not exist: %s',
                                $baseDir
                            ));
                            continue;
                        }

                        $translator->addTranslationFilePattern(
                            $type,
                            $baseDir,
                            $patternStr,
                            'default'  // text domain
                        );
                    }
                }

                return $translator;
            },

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
            OurBotsHandler::class           => OurBotsHandlerFactory::class,
            AuthenticationMiddleware::class => AuthenticationMiddlewareFactory::class,
            ApiHandler::class               => ApiHandlerFactory::class,
            ProfileHandler::class           => ProfileHandlerFactory::class,
            LoginHandler::class             => LoginHandlerFactory::class,
            LogoutHandler::class            => LogoutHandlerFactory::class,
            RegisterHandler::class          => RegisterHandlerFactory::class,

            // Repositories
            UserRepository::class    => UserRepositoryFactory::class,
            MtgoBotRepository::class => MtgoBotRepositoryFactory::class,

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
