<?php

declare(strict_types=1);

namespace AppTest\Handler\Auth;

use App\Entity\User;
use App\Handler\Auth\RegisterHandler;
use App\Repository\UserRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function password_verify;

class RegisterHandlerTest extends TestCase
{
    /** @var TemplateRendererInterface|MockObject */
    private $renderer;

    /** @var UserRepository|MockObject */
    private $userRepository;

    /** @var RegisterHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->renderer       = $this->createMock(TemplateRendererInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->handler = new RegisterHandler($this->renderer, $this->userRepository);
    }

    public function testGetRequestShowsRegistrationForm(): void
    {
        $request = new ServerRequest();

        $this->renderer->expects($this->once())
            ->method('render')
            ->with('app::auth/register', [
                'formData' => [],
                'errors'   => [],
            ])
            ->willReturn('registration form');

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame('registration form', (string) $response->getBody());
    }

    public function testPostWithInvalidDataShowsErrors(): void
    {
        $request = (new ServerRequest())
            ->withMethod('POST')
            ->withParsedBody([
                'email'    => 'invalid-email',
                'name'     => '',
                'password' => 'short',
            ]);

        $this->renderer->expects($this->once())
            ->method('render')
            ->with('app::auth/register', $this->callback(function ($params) {
                $this->assertArrayHasKey('errors', $params);
                $this->assertArrayHasKey('email', $params['errors']);
                $this->assertArrayHasKey('name', $params['errors']);
                $this->assertArrayHasKey('password', $params['errors']);
                return true;
            }))
            ->willReturn('form with errors');

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame('form with errors', (string) $response->getBody());
    }

    public function testPostWithDuplicateEmailShowsError(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneByEmail')
            ->with('existing@example.com')
            ->willReturn(new User('existing@example.com', 'Existing User', 'hashedpassword'));

        $request = (new ServerRequest())
            ->withMethod('POST')
            ->withParsedBody([
                'email'    => 'existing@example.com',
                'name'     => 'Test User',
                'password' => 'validpassword123',
            ]);

        $this->renderer->expects($this->once())
            ->method('render')
            ->with('app::auth/register', $this->callback(function ($params) {
                $this->assertArrayHasKey('errors', $params);
                $this->assertArrayHasKey('email', $params['errors']);
                $this->assertSame('Email already registered', $params['errors']['email']);
                return true;
            }))
            ->willReturn('form with duplicate email error');

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame('form with duplicate email error', (string) $response->getBody());
    }

    public function testPostWithValidDataCreatesUserAndRedirects(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneByEmail')
            ->with('newuser@example.com')
            ->willReturn(null);

        $this->userRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $user) {
                $this->assertSame('newuser@example.com', $user->getEmail());
                $this->assertSame('New User', $user->getName());
                $this->assertTrue(password_verify('validpassword123', $user->getPassword()));
                return true;
            }), true);

        $request = (new ServerRequest())
            ->withMethod('POST')
            ->withParsedBody([
                'email'            => 'newuser@example.com',
                'name'             => 'New User',
                'password'         => 'validpassword123',
                'confirm_password' => 'validpassword123',
            ]);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/login?registered=1', $response->getHeaderLine('Location'));
    }
}
