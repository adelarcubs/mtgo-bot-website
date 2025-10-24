<?php

declare(strict_types=1);

namespace AppTest\Handler\Auth;

use App\Handler\Auth\LogoutHandler;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Session\Session;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use PHPUnit\Framework\TestCase;

class LogoutHandlerTest extends TestCase
{
    private LogoutHandler $handler;
    private string $loginUrl = '/login';

    protected function setUp(): void
    {
        $this->handler = new LogoutHandler($this->loginUrl);
    }

    public function testHandleClearsSessionAndRedirectsToLogin(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
            ->method('clear');
        $session->expects($this->once())
            ->method('regenerate');

        $request = (new ServerRequest())
            ->withAttribute(SessionMiddleware::SESSION_ATTRIBUTE, $session);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame($this->loginUrl, $response->getHeaderLine('Location'));
    }

    public function testHandleWorksWithRealSession(): void
    {
        $session = new Session(['user_id' => 123]);

        $request = (new ServerRequest())
            ->withAttribute(SessionMiddleware::SESSION_ATTRIBUTE, $session);

        $this->assertTrue($session->has('user_id'));

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame($this->loginUrl, $response->getHeaderLine('Location'));
        $this->assertFalse($session->has('user_id'));
    }

    public function testHandleWorksWithoutSession(): void
    {
        $request = new ServerRequest();

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame($this->loginUrl, $response->getHeaderLine('Location'));
    }
}
