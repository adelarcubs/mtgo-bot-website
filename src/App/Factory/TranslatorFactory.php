<?php

declare(strict_types=1);

namespace App\Factory;

use Laminas\I18n\Translator\Loader\Gettext;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorInterface;
use Psr\Container\ContainerInterface;

use function error_log;
use function is_dir;
use function sprintf;

class TranslatorFactory
{
    public function __invoke(ContainerInterface $container): TranslatorInterface
    {
        $config     = $container->get('config');
        $translator = new Translator();

        // Set default locale from config
        $defaultLocale = $config['i18n']['default_locale'];
        $translator->setLocale($defaultLocale);
        $translator->setFallbackLocale($defaultLocale);

        // Enable event manager for locale change events
        $translator->enableEventManager();

        // Add translation files if configured
        if (isset($config['i18n']['translation_file_patterns'])) {
            $pattern = $config['i18n']['translation_file_patterns'];

            $loader = new Gettext();
            $translator->getPluginManager()->setService('gettext', $loader);

            $type       = $pattern['type'];
            $baseDir    = $pattern['base_dir'];
            $patternStr = $pattern['pattern'];

            // Ensure base directory exists
            if (! is_dir($baseDir)) {
                error_log(sprintf(
                    'Translation base directory does not exist: %s',
                    $baseDir
                ));
            }

            $translator->addTranslationFilePattern(
                $type,
                $baseDir,
                $patternStr,
                'default'  // text domain
            );
        }

        return $translator;
    }
}
