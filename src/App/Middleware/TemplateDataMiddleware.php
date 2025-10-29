<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\I18n\Translator\Translator;
use Mezzio\Authentication\UserInterface;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TemplateDataMiddleware implements MiddlewareInterface
{
    private TemplateRendererInterface $renderer;
    private Translator $translator;
    private ContainerInterface $container;

    public function __construct(
        TemplateRendererInterface $renderer,
        Translator $translator,
        ContainerInterface $container
    ) {
        $this->renderer   = $renderer;
        $this->translator = $translator;
        $this->container  = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user        = $request->getAttribute(UserInterface::class);
        $routeMatch  = $request->getAttribute(RouteResult::class);
        $routeParams = $routeMatch ? $routeMatch->getMatchedParams() : [];

        // Get the current route
        $routeMatch = $request->getAttribute(RouteResult::class);
        $routeName  = $routeMatch ? $routeMatch->getMatchedRouteName() : 'home';

        // Get the current locale from the request or use the default
        $config        = $this->container->get('config');
        $defaultLocale = $config['i18n']['default_locale'];
        $locale        = $request->getAttribute('locale', $defaultLocale);

        // Get supported locales for language switcher
        $supportedLocales = $config['i18n']['supported_locales'] ?? [];

        // If this is a localized route, get the base route name
        // if (strpos($routeName, 'localized.') === 0) {
        // $routeName = substr($routeName, 9); // Remove 'localized.' prefix
        // }
        // Set the translator locale if it's different
        if ($this->translator->getLocale() !== $locale) {
            $this->translator->setLocale($locale);
        }

        // Add default parameters to all templates
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'logged_in_user',
            $user
        );

        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'current_route',
            $routeName
        );

        // Add i18n related parameters
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'current_locale',
            $locale
        );

        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'supported_locales',
            $supportedLocales
        );

        // Add current route name for language switcher
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'route_name',
            $routeName
        );

        // Add translator to templates
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'translate',
            $this->translator
        );

        // Also add as 'translator' for compatibility
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'translator',
            $this->translator
        );

        // Add the translator as a global variable
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            't',
            $this->translator
        );

        return $handler->handle($request);
    }
}
