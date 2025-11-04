<?php

declare(strict_types=1);

namespace App\Handler\MyCollection;

use App\Entity\User;
use App\Repository\UserCollectionItemRepository;
use App\Repository\UserRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MyCollectionHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly UserCollectionItemRepository $collectionItemRepository,
        private readonly UserRepository $userRepository,
        private readonly TemplateRendererInterface $renderer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userId = $request->getAttribute('user_id');

        if (! $userId) {
            // Handle unauthenticated user
            return new HtmlResponse($this->renderer->render('error::401'), 401);
        }

        /** @var User|null $user */
        $user = $this->userRepository->find($userId);

        if (! $user) {
            return new HtmlResponse($this->renderer->render('error::404'), 404);
        }

        // Get all user's collection items with quantity > 0
        $collectionItems = $this->collectionItemRepository->findNonZeroQuantityByUser($user);

        return new HtmlResponse($this->renderer->render('app::my-collection/index', [
            'collectionItems' => $collectionItems,
        ]));
    }
}
