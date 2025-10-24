<?php

declare(strict_types=1);

namespace App\Twig;

use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;

use function gettype;
use function is_object;
use function sprintf;

class TranslationExtensionFactory
{
    public function __invoke(ContainerInterface $container): TranslationExtension
    {
        if (! $container->has(TranslatorInterface::class) && ! $container->has(Translator::class)) {
            throw new RuntimeException(sprintf(
                'Failed to create %s; could not find %s service.',
                TranslationExtension::class,
                TranslatorInterface::class
            ));
        }

        $translator = $container->has(TranslatorInterface::class)
            ? $container->get(TranslatorInterface::class)
            : $container->get(Translator::class);

        if (! $translator instanceof TranslatorInterface) {
            throw new RuntimeException(sprintf(
                'Service resolved from container must implement %s, %s given',
                TranslatorInterface::class,
                is_object($translator) ? $translator::class : gettype($translator)
            ));
        }

        return new TranslationExtension($translator);
    }
}
