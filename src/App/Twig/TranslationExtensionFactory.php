<?php

declare(strict_types=1);

namespace App\Twig;

use Laminas\I18n\Translator\Translator;
use Psr\Container\ContainerInterface;

class TranslationExtensionFactory
{
    public function __invoke(ContainerInterface $container): TranslationExtension
    {
        return new TranslationExtension(
            $container->get(Translator::class)
        );
    }
}
