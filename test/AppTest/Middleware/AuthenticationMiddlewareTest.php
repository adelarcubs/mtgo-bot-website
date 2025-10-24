<?php

declare(strict_types=1);

namespace AppTest\Middleware;

use App\Entity\User;
use App\Middleware\AuthenticationMiddleware;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Mezzio\Session\Session;
use Mezzio\Session\SessionMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function base64_encode;

class AuthenticationMiddlewareTest extends TestCase
{
    private AuthenticationMiddleware $middleware;
    private array $config;
    private RequestHandlerInterface|MockObject $handler;

    protected function setUp(): void
    {
        $this->config = [
            'realm'         => 'Test Realm',
            'login_url'     => '/login',
            'public_routes' => ['/public', '/assets/*'],
            'tokens'        => [
                'valid-token' => [
                    'description' => 'Valid Token',
                    'scopes'      => ['api:read'],
                ],
            ],
        ];

        $this->middleware = new AuthenticationMiddleware($this->config);
        $this->handler    = $this->createMock(RequestHandlerInterface::class);
    }

    private function createRequestWithSession(string $path, ?array $sessionData = null, array $headers = []): ServerRequest
    {
        $session = new Session($sessionData ?? []);
        $request = (new ServerRequest())
            ->withUri(new Uri($path))
            ->withMethod('GET')
            ->withHeader('Accept', 'application/json')
            ->withAttribute(SessionMiddleware::SESSION_ATTRIBUTE, $session);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $request;
    }

    public function testPublicRouteIsAllowedWithoutAuthentication(): void
    {
        $request = $this->createRequestWithSession('/public');

        $this->handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $response = $this->middleware->process($request, $this->handler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testApiRouteWithValidBearerToken(): void
    {
        $request = $this->createRequestWithSession(
            '/api/test',
            [],
            ['Authorization' => 'Bearer valid-token']
        );

        $this->handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $response = $this->middleware->process($request, $this->handler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testApiRouteWithInvalidToken(): void
    {
        $request = $this->createRequestWithSession(
            '/api/test',
            [],
            ['Authorization' => 'Bearer invalid-token']
        );

        $response = $this->middleware->process($request, $this->handler);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(401, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            '{"error":"Unauthorized - Invalid token"}',
            (string) $response->getBody()
        );
    }

    public function testWebRouteWithValidSession(): void
    {
        $user    = new User('test@example.com', 'Test User', 'password');
        $session = new Session(['user' => $user]);

        $request = (new ServerRequest())
            ->withUri(new Uri('/protected'))
            ->withMethod('GET')
            ->withAttribute(SessionMiddleware::SESSION_ATTRIBUTE, $session);

        $this->handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $response = $this->middleware->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);

        // Check that the user is set in the session
        $session         = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $userFromSession = $session->get('user');
        $this->assertInstanceOf(User::class, $userFromSession);
        $this->assertSame('test@example.com', $userFromSession->getEmail());
    }

    public function testWebRouteWithoutSessionRedirectsToLogin(): void
    {
        $request = $this->createRequestWithSession('/protected');

        $response = $this->middleware->process($request, $this->handler);

        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/login', $response->getHeaderLine('Location'));
        $this->assertSame('/protected', $session->get('redirect_after_login'));
    }

    public function testPublicPathPrefixMatching(): void
    {
        $request = $this->createRequestWithSession('/assets/images/logo.png');

        $this->handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $response = $this->middleware->process($request, $this->handler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testApiRouteWithBasicAuth(): void
    {
        $request = $this->createRequestWithSession(
            '/api/test',
            [],
            ['Authorization' => 'Basic ' . base64_encode('valid-token')]
        );

        $this->handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $response = $this->middleware->process($request, $this->handler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
