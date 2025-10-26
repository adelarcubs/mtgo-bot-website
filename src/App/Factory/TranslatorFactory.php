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
        $defaultLocale = $config['i18n']['default_locale'] ?? 'pt_BR';
        $translator->setLocale($defaultLocale);
        $translator->setFallbackLocale($defaultLocale);

        // Enable event manager for locale change events
        $translator->enableEventManager();

        // Add translation files if configured
        if (isset($config['i18n']['translation_file_patterns'])) {
            $loader = new Gettext();
            $translator->getPluginManager()->setService('gettext', $loader);

            foreach ($config['i18n']['translation_file_patterns'] as $pattern) {
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
    }
}
