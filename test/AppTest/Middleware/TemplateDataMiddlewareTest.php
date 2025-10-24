<?php

declare(strict_types=1);

namespace AppTest\Middleware;

use App\Middleware\TemplateDataMiddleware;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequest;
use Laminas\I18n\Translator\Translator;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TemplateDataMiddlewareTest extends TestCase
{
    private TemplateRendererInterface|MockObject $renderer;
    private Translator|MockObject $translator;
    private ContainerInterface|MockObject $container;
    private TemplateDataMiddleware $middleware;
    private RequestHandlerInterface|MockObject $handler;
    private ServerRequest $request;

    protected function setUp(): void
    {
        $this->renderer   = $this->createMock(TemplateRendererInterface::class);
        $this->translator = $this->createMock(Translator::class);
        $this->container  = $this->createMock(ContainerInterface::class);
        $this->handler    = $this->createMock(RequestHandlerInterface::class);

        $this->middleware = new TemplateDataMiddleware(
            $this->renderer,
            $this->translator,
            $this->container
        );

        $this->request = new ServerRequest();
    }

    public function testProcessAddsDefaultTemplateData(): void
    {
        $config = [
            'i18n' => [
                'default_locale'    => 'en_US',
                'supported_locales' => ['en_US', 'pt_BR'],
            ],
        ];

        $this->container->method('get')
            ->with('config')
            ->willReturn($config);

        $response = new TextResponse('Test');
        $this->handler->expects($this->once())
            ->method('handle')
            ->willReturn($response);

        // The middleware should add several template parameters
        $this->renderer->expects($this->atLeast(5))
            ->method('addDefaultParam');

        $result = $this->middleware->process($this->request, $this->handler);

        $this->assertSame($response, $result);
    }

    public function testProcessAddsUserDataWhenAuthenticated(): void
    {
        $config = [
            'i18n' => [
                'default_locale'    => 'en_US',
                'supported_locales' => ['en_US', 'pt_BR'],
            ],
        ];

        // Create a mock user
        $user = $this->getMockBuilder('Mezzio\Authentication\UserInterface')
            ->getMock();

        $request = $this->request->withAttribute('Mezzio\Authentication\UserInterface', $user);

        $this->container->method('get')
            ->with('config')
            ->willReturn($config);

        $response = new TextResponse('Test');
        $this->handler->expects($this->once())
            ->method('handle')
            ->willReturn($response);

        // Record all calls to addDefaultParam
        $calls = [];
        $this->renderer->method('addDefaultParam')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $calls[] = $args;
            });

        $result = $this->middleware->process($request, $this->handler);

        // Verify the response
        $this->assertSame($response, $result);

        // Verify that the user was added to the template data
        $userFound = false;
        foreach ($calls as $call) {
            if ($call[1] === 'logged_in_user' && $call[2] === $user) {
                $userFound = true;
                break;
            }
        }
        $this->assertTrue($userFound, 'User was not added to template data');
    }

    public function testProcessAddsRouteDataWhenRouteExists(): void
    {
        $config = [
            'i18n' => [
                'default_locale'    => 'en_US',
                'supported_locales' => ['en_US', 'pt_BR'],
            ],
        ];

        $routeResult = $this->createMock(RouteResult::class);
        $routeResult->method('getMatchedRouteName')->willReturn('test.route');
        $routeResult->method('getMatchedParams')->willReturn(['id' => 123]);

        $request = $this->request->withAttribute(RouteResult::class, $routeResult);

        $this->container->method('get')
            ->with('config')
            ->willReturn($config);

        $response = new TextResponse('Test');
        $this->handler->expects($this->once())
            ->method('handle')
            ->willReturn($response);

        // Record all calls to addDefaultParam
        $calls = [];
        $this->renderer->method('addDefaultParam')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $calls[] = $args;
            });

        $result = $this->middleware->process($request, $this->handler);

        // Verify the response
        $this->assertSame($response, $result);

        // Verify that the route name was added to the template data
        $routeNameFound = false;
        foreach ($calls as $call) {
            if ($call[1] === 'route_name' && $call[2] === 'test.route') {
                $routeNameFound = true;
                break;
            }
        }
        $this->assertTrue($routeNameFound, 'Route name was not added to template data');
    }
}
