<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\MyAccountHandler;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Session\Session;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class MyAccountHandlerTest extends TestCase
{
    /** @var TemplateRendererInterface|MockObject */
    private $renderer;

    /** @var MyAccountHandler */
    private $handler;

    /** @var ServerRequestInterface|MockObject */
    private $request;

    /** @var SessionInterface */
    private $session;

    protected function setUp(): void
    {
        $this->renderer = $this->createMock(TemplateRendererInterface::class);
        $this->handler  = new MyAccountHandler($this->renderer);
        $this->session  = new Session([]);

        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->request->method('getAttribute')
            ->with(SessionMiddleware::SESSION_ATTRIBUTE)
            ->willReturn($this->session);
    }

    public function testHandleRedirectsToLoginWhenUserNotAuthenticated(): void
    {
        $response = $this->handler->handle($this->request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
    }

    public function testHandleRendersTemplateWhenUserIsAuthenticated(): void
    {
        // Set up authenticated user in session
        $this->session->set('user', [
            'id'    => 'test-user',
            'email' => 'test@example.com',
            'name'  => 'Test User',
            'roles' => ['user'],
        ]);

        $this->renderer->expects($this->once())
            ->method('render')
            ->with(
                'app::my-account',
                $this->arrayHasKey('user')
            )
            ->willReturn('Rendered template');

        $response = $this->handler->handle($this->request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('Rendered template', (string) $response->getBody());
    }

    public function testHandleUsesCustomLoginUrlWhenProvided(): void
    {
        $handler  = new MyAccountHandler($this->renderer, '/custom-login');
        $response = $handler->handle($this->request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/custom-login', $response->getHeaderLine('Location'));
    }

    public function testHandlePassesCorrectUserDataToTemplate(): void
    {
        $userData = [
            'id'    => 'test-user',
            'email' => 'test@example.com',
            'name'  => 'Test User',
            'roles' => ['admin', 'user'],
        ];

        $this->session->set('user', $userData);

        $this->renderer->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($template, $data) {
                $this->assertEquals('app::my-account', $template);
                $this->assertArrayHasKey('user', $data);
                $this->assertEquals('test-user', $data['user']['identity']);
                $this->assertEquals('test@example.com', $data['user']['details']['email']);
                $this->assertEquals('Test User', $data['user']['details']['name']);
                $this->assertEquals(['admin', 'user'], $data['user']['roles']);
                return '';
            });

        $this->handler->handle($this->request);
    }
}
