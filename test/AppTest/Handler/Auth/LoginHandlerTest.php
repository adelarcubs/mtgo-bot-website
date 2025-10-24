<?php

declare(strict_types=1);

namespace AppTest\Handler\Auth;

use App\Entity\User;
use App\Handler\Auth\LoginHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Session\Session;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function password_hash;

use const PASSWORD_DEFAULT;

class LoginHandlerTest extends TestCase
{
    /** @var TemplateRendererInterface|MockObject */
    private $renderer;

    /** @var EntityManager|MockObject */
    private $entityManager;

    /** @var LoginHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->renderer      = $this->createMock(TemplateRendererInterface::class);
        $this->entityManager = $this->createMock(EntityManager::class);

        $this->handler = new LoginHandler($this->renderer, $this->entityManager);
    }

    public function testGetRequestShowsLoginForm(): void
    {
        $request = new ServerRequest();

        $this->renderer->expects($this->once())
            ->method('render')
            ->with('app::auth/login', [
                'error' => null,
                'email' => '',
            ])
            ->willReturn('rendered form');

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame('rendered form', (string) $response->getBody());
    }

    public function testPostWithInvalidCredentialsShowsError(): void
    {
        $request = (new ServerRequest())
            ->withMethod('POST')
            ->withParsedBody([
                'email'    => 'user@example.com',
                'password' => 'wrongpassword',
            ]);

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->createMock(ObjectRepository::class));

        $this->renderer->expects($this->once())
            ->method('render')
            ->with('app::auth/login', [
                'error' => 'Invalid credentials. Please try again.',
                'email' => 'user@example.com',
            ])
            ->willReturn('login with error');

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame('login with error', (string) $response->getBody());
    }

    public function testPostWithValidCredentialsRedirectsToMyAccount(): void
    {
        $user = new User('user@example.com', 'Test User', password_hash('validpassword', PASSWORD_DEFAULT));
        // Use reflection to set the ID since it's private and has no setter
        $reflection = new ReflectionClass($user);
        $property   = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);

        $request         = (new ServerRequest())
            ->withMethod('POST')
            ->withAttribute(
                SessionMiddleware::SESSION_ATTRIBUTE,
                $session = new Session([])
            )
            ->withParsedBody([
                'email'    => 'user@example.com',
                'password' => 'validpassword',
            ]);

        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'user@example.com'])
            ->willReturn($user);

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/my-account', $response->getHeaderLine('Location'));
        $this->assertSame(1, $session->get('user')['id']);
        $this->assertSame('user@example.com', $session->get('user')['email']);
        $this->assertSame('Test User', $session->get('user')['name']);
    }
}
