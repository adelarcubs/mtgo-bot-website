<?php

declare(strict_types=1);

namespace App\Handler\MyCollection;

use App\Entity\User;
use App\Entity\UserCollectionItem;
use App\Repository\MtgoItemRepository;
use App\Repository\UserCollectionItemRepository;
use App\Repository\UserRepository;
use App\Service\DekFileReader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\UploadedFile;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

use function error_log;
use function file_exists;
use function is_dir;
use function mkdir;
use function sprintf;
use function uniqid;
use function unlink;

use const UPLOAD_ERR_OK;

class UploadCollectionHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly UserCollectionItemRepository $collectionItemRepository,
        private readonly UserRepository $userRepository,
        private readonly TemplateRendererInterface $renderer,
        private readonly string $uploadPath,
        private readonly EntityManagerInterface $entityManager,
        private readonly DekFileReader $dekFileReader,
        private readonly MtgoItemRepository $mtgoItemRepository
    ) {
        if (! is_dir($this->uploadPath) && ! @mkdir($this->uploadPath, 0777, true)) {
            throw new RuntimeException(sprintf('Upload directory "%s" does not exist and could not be created', $this->uploadPath));
        }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userId = $request->getAttribute('user_id');

        if (! $userId) {
            return new HtmlResponse($this->renderer->render('error::401'), 401);
        }

        $user = $this->userRepository->find($userId);

        if (! $user) {
            return new HtmlResponse($this->renderer->render('error::404'), 404);
        }

        if ($request->getMethod() === 'POST') {
            $uploadedFile = $request->getUploadedFiles()['dek_file'] ?? null;

            if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
                $this->handleFileUpload($uploadedFile, $user);
            }

            return new RedirectResponse('/my-collection');
        }

        return new HtmlResponse($this->renderer->render('app::my-collection/upload', [
            'user' => $user,
        ]));
    }

    private function handleFileUpload(UploadedFile $uploadedFile, User $user): void
    {
        try {
            // Save the uploaded file temporarily
            $tempFilePath = $this->uploadPath . '/' . uniqid('dek_', true) . '.dek';
            $uploadedFile->moveTo($tempFilePath);

            try {
                // Read and process the .dek file
                $cards = $this->dekFileReader->readDekFile($tempFilePath);

                // Process each card from the .dek file
                foreach ($cards as $card) {
                    $mtgoItem = $this->mtgoItemRepository->find($card->mtgoItemId);
                    if (! $mtgoItem) {
                        //throw new RuntimeException(sprintf('MtgoItem with id "%s" not found', $card->mtgoItemId));
                        continue;
                    }
                    $quantity       = $card->quantity;
                    $collectionItem = new UserCollectionItem($user, $mtgoItem, $quantity);

                    $this->collectionItemRepository->save($collectionItem, false);
                }

                $this->entityManager->flush();
            } finally {
                // Clean up the temporary file
                if (file_exists($tempFilePath)) {
                    unlink($tempFilePath);
                }
            }
        } catch (Exception $e) {
            error_log(sprintf('Error processing .dek file: %s', $e->getMessage()));
            throw $e; // Re-throw to be handled by the error handler
        }
    }
}
