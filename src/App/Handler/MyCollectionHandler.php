<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\MtgoItem;
use App\Entity\User;
use App\Entity\UserCollectionItem;
use App\Repository\UserCollectionItemRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\UploadedFile;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

use function is_dir;
use function mkdir;
use function sprintf;

use const UPLOAD_ERR_OK;

class MyCollectionHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly UserCollectionItemRepository $collectionItemRepository,
        private readonly UserRepository $userRepository,
        private readonly TemplateRendererInterface $renderer,
        private readonly string $uploadPath,
        private readonly EntityManagerInterface $entityManager
    ) {
        if (! is_dir($this->uploadPath) && ! @mkdir($this->uploadPath, 0777, true)) {
            throw new RuntimeException(sprintf('Upload directory "%s" does not exist and could not be created', $this->uploadPath));
        }
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

        if ($request->getMethod() === 'POST') {
            $uploadedFile = $request->getUploadedFiles()['dek_file'] ?? null;
            $name         = $request->getParsedBody()['name'] ?? '';

            if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK && $name) {
                $this->handleFileUpload($uploadedFile, $name, $user);
            }

            return new RedirectResponse('/my-collection');
        }

        $collectionItems = $this->collectionItemRepository->findByUser($user);

        return new HtmlResponse($this->renderer->render('app::my-collection', [
            'collectionItems' => $collectionItems,
        ]));
    }

    private function handleFileUpload(UploadedFile $uploadedFile, string $name, User $user): void
    {
        // TODO: Parse the .dek file to get card information
        // For now, we'll just create a sample item with a hardcoded MtgoItem

        // In a real implementation, you would:
        // 1. Parse the .dek file to get card information
        // 2. Find or create the corresponding MtgoItem
        // 3. Create a UserCollectionItem with the MtgoItem and quantity

        // Example with hardcoded values (replace with actual parsing logic)
        $mtgoItem = $this->entityManager->getRepository(MtgoItem::class)->find(1); // Get a sample MtgoItem

        if (! $mtgoItem) {
            throw new RuntimeException('MtgoItem not found');
        }

        $collectionItem = new UserCollectionItem(
            user: $user,
            mtgoItem: $mtgoItem,
            quantity: 1 // Get actual quantity from .dek file
        );

        $this->collectionItemRepository->save($collectionItem);
    }
}
