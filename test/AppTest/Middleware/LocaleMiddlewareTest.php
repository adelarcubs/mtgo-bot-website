<?php

declare(strict_types=1);

namespace AppTest\Middleware;

use App\Middleware\LocaleMiddleware;
use Laminas\Diactoros\ServerRequest;
use Laminas\I18n\Translator\Translator;
use Mezzio\Session\Session;
use Mezzio\Session\SessionMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LocaleMiddlewareTest extends TestCase
{
    /** @var Translator&MockObject */
    private $translator;

    /** @var ContainerInterface&MockObject */
    private $container;

    private LocaleMiddleware $middleware;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(Translator::class);
        $this->container  = $this->createMock(ContainerInterface::class);

        $this->middleware = new LocaleMiddleware($this->translator, $this->container);
    }

    public function testProcessWithLocaleInQueryParams(): void
    {
        $session = new Session([]);
        $request = (new ServerRequest())
            ->withQueryParams(['locale' => 'pt_BR'])
            ->withAttribute(SessionMiddleware::SESSION_ATTRIBUTE, $session);

        $handler  = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->container->method('get')
            ->with('config')
            ->willReturn([
                'i18n' => [
                    'supported_locales' => ['en_US' => 'English', 'pt_BR' => 'Português'],
                    'default_locale'    => 'en_US',
                ],
            ]);

        $this->translator->expects($this->once())
            ->method('setLocale')
            ->with('pt_BR');

        $handler->expects($this->once())
            ->method('handle')
            ->willReturnCallback(function ($request) use ($response) {
                $this->assertEquals('pt_BR', $request->getAttribute('locale'));
                return $response;
            });

        $result = $this->middleware->process($request, $handler);

        $this->assertSame($response, $result);
        $this->assertEquals('pt_BR', $session->get('locale'));
    }

    public function testProcessWithLocaleInSession(): void
    {
        $session = new Session(['locale' => 'es_ES']);
        $request = (new ServerRequest())
            ->withAttribute(SessionMiddleware::SESSION_ATTRIBUTE, $session);

        $handler  = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->container->method('get')
            ->with('config')
            ->willReturn([
                'i18n' => [
                    'supported_locales' => ['en_US' => 'English', 'es_ES' => 'Español'],
                    'default_locale'    => 'en_US',
                ],
            ]);

        $this->translator->expects($this->once())
            ->method('setLocale')
            ->with('es_ES');

        $handler->expects($this->once())
            ->method('handle')
            ->willReturn($response);

        $result = $this->middleware->process($request, $handler);

        $this->assertSame($response, $result);
    }

    public function testProcessWithUnsupportedLocale(): void
    {
        $session = new Session([]);
        $request = (new ServerRequest())
            ->withQueryParams(['locale' => 'fr_FR'])
            ->withAttribute(SessionMiddleware::SESSION_ATTRIBUTE, $session);

        $handler  = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->container->method('get')
            ->with('config')
            ->willReturn([
                'i18n' => [
                    'supported_locales' => ['en_US' => 'English', 'pt_BR' => 'Português'],
                    'default_locale'    => 'en_US',
                ],
            ]);

        $this->translator->expects($this->once())
            ->method('setLocale')
            ->with('en_US');

        $handler->expects($this->once())
            ->method('handle')
            ->willReturn($response);

        $result = $this->middleware->process($request, $handler);

        $this->assertSame($response, $result);
        $this->assertEquals('en_US', $session->get('locale'));
    }
}
