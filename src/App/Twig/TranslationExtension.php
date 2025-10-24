<?php

declare(strict_types=1);

namespace App\Twig;

use Laminas\I18n\Translator\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TranslationExtension extends AbstractExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('translate', [$this, 'translate']),
            new TwigFunction('trans', [$this, 'translate']), // Alias for 'translate'
        ];
    }

    public function translate(string $message, array $params = [], string $textDomain = 'default', ?string $locale = null): string
    {
        $translated = $this->translator->translate($message, $textDomain, $locale);

        // If no translation is found, return the original message
        return $translated !== $message ? $translated : $message;
    }
}
