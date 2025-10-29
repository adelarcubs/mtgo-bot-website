<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;
use App\Repository\RentedCardRepository;
use App\Repository\UserRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MyRentHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $template,
        private RentedCardRepository $rentedCardRepository,
        private UserRepository $userRepository
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userId = $request->getAttribute('user_id');

        if (! $userId) {
            // Handle unauthenticated user
            return new HtmlResponse($this->template->render('error::401'), 401);
        }

        /** @var User|null $user */
        $user = $this->userRepository->find($userId);

        if (! $user) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        }

        $rentedCards = $this->rentedCardRepository->findRentedCardsByUserWithDetails($user);

        return new HtmlResponse(
            $this->template->render('app::my-rent', [
                'rentedCards' => $rentedCards,
            ])
        );
    }
}
