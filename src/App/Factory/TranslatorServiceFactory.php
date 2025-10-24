<?php

declare(strict_types=1);

namespace App\Factory;

use Laminas\I18n\Translator\Loader\Gettext;
use Laminas\I18n\Translator\Translator;
use Psr\Container\ContainerInterface;

class TranslatorServiceFactory
{
    public function __invoke(ContainerInterface $container): Translator
    {
        $config     = $container->get('config');
        $translator = new Translator();
        echo "oi";
        // Configure the translator
        $translator->setLocale($config['translator']['locale']);
        $translator->setFallbackLocale('en_US');

        // Add translation files
        if (isset($config['translator']['translation_file_patterns'])) {
            foreach ($config['translator']['translation_file_patterns'] as $pattern) {
                $translator->getPluginManager()->setService('default', new Gettext());
                $translator->addTranslationFilePattern(
                    $pattern['type'],
                    $pattern['base_dir'],
                    $pattern['pattern']
                );
            }
        }

        return $translator;
    }
}
