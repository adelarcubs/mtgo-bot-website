<?php

declare(strict_types=1);

namespace App\Handler\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function filter_var;
use function password_hash;
use function strlen;

use const FILTER_VALIDATE_EMAIL;
use const PASSWORD_DEFAULT;

class RegisterHandler implements RequestHandlerInterface
{
    private TemplateRendererInterface $template;
    private UserRepository $userRepository;

    public function __construct(
        TemplateRendererInterface $template,
        UserRepository $userRepository
    ) {
        $this->template       = $template;
        $this->userRepository = $userRepository;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            return $this->handleRegistration($request);
        }

        // Show empty form for GET request
        return new HtmlResponse(
            $this->template->render('app::auth/register', [
                'formData' => [],
                'errors'   => [],
            ])
        );
    }

    private function handleRegistration(ServerRequestInterface $request): ResponseInterface
    {
        $data     = $request->getParsedBody();
        $formData = [
            'email' => $data['email'] ?? '',
            'name'  => $data['name'] ?? '',
        ];

        $errors = $this->validateRegistration($data);

        if (empty($errors)) {
            $user = new User();
            $user->setEmail($formData['email']);
            $user->setName($formData['name']);
            $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));

            $this->userRepository->save($user, true);

            // Redirect to login page with success message
            return new RedirectResponse('/login?registered=1');
        }

        // Show form with errors
        return new HtmlResponse(
            $this->template->render('app::auth/register', [
                'formData' => $formData,
                'errors'   => $errors,
            ])
        );
    }

    private function validateRegistration(array $data): array
    {
        $errors = [];

        // Validate email
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } elseif ($this->userRepository->findOneBy(['email' => $data['email']])) {
            $errors['email'] = 'Email already registered';
        }

        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'Name must be at least 2 characters';
        }

        // Validate password
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }

        // Validate password confirmation
        if (empty($data['confirm_password'])) {
            $errors['confirm_password'] = 'Please confirm your password';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        return $errors;
    }
}
