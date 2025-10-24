<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\I18n\Translator\Translator;
use Mezzio\Session\SessionMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LocaleMiddleware implements MiddlewareInterface
{
    private Translator $translator;
    private ContainerInterface $container;

    public function __construct(Translator $translator, ContainerInterface $container)
    {
        $this->translator = $translator;
        $this->container  = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session     = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $queryParams = $request->getQueryParams();

        // Get the config from the container
        $config           = $this->container->get('config');
        $supportedLocales = $config['i18n']['supported_locales'] ?? [];
        $defaultLocale    = $config['i18n']['default_locale'] ?? 'en_US';

        // Try to get locale from URL parameters first
        $locale = $queryParams['locale'] ?? null;

        // If not in URL, try to get it from session
        if (empty($locale)) {
            $locale = $session->get('locale') ?? $defaultLocale;
        }

        // Validate the locale against supported locales
        if (! isset($supportedLocales[$locale])) {
            $locale = $defaultLocale;
        }
        // Save the locale in session for future requests
        $session->set('locale', $locale);

        // Set the locale in the translator
        $this->translator->setLocale($locale);

        // Add locale to request attributes for use in templates and other middleware
        $request = $request->withAttribute('locale', $locale);

        return $handler->handle($request);
    }
}
