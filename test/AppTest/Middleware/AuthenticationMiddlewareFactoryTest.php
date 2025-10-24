<?php

declare(strict_types=1);

namespace AppTest\Middleware;

use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthenticationMiddlewareFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class AuthenticationMiddlewareFactoryTest extends TestCase
{
    public function testFactoryCreatesInstance(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $config    = [
            'authentication' => [
                'realm'         => 'Test Realm',
                'public_routes' => ['/public'],
                'login_url'     => '/login',
                'tokens'        => [
                    'test-token' => [
                        'description' => 'Test Token',
                        'scopes'      => ['test:read'],
                    ],
                ],
            ],
        ];

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory    = new AuthenticationMiddlewareFactory();
        $middleware = $factory($container);

        $this->assertInstanceOf(AuthenticationMiddleware::class, $middleware);
    }

    public function testFactoryWithEmptyConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn([]);

        $factory    = new AuthenticationMiddlewareFactory();
        $middleware = $factory($container);

        $this->assertInstanceOf(AuthenticationMiddleware::class, $middleware);
    }
}
