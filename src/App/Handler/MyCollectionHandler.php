<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;
use App\Entity\UserCollection;
use App\Repository\UserCollectionRepository;
use App\Repository\UserRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\UploadedFile;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MyCollectionHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly UserCollectionRepository $collectionRepository,
        private readonly UserRepository $userRepository,
        private readonly TemplateRendererInterface $renderer,
        private readonly string $uploadPath
    ) {
        if (!is_dir($this->uploadPath) && !@mkdir($this->uploadPath, 0777, true)) {
            throw new \RuntimeException(sprintf('Upload directory "%s" does not exist and could not be created', $this->uploadPath));
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
            $name = $request->getParsedBody()['name'] ?? '';
            
            if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK && $name) {
                $this->handleFileUpload($uploadedFile, $name, $user);
            }
            
            return new RedirectResponse('/my-collection');
        }
        
        $collections = $this->collectionRepository->findByUser($user);
        
        return new HtmlResponse($this->renderer->render('app::my-collection', [
            'collections' => $collections,
        ]));
    }
    
    private function handleFileUpload(UploadedFile $uploadedFile, string $name, User $user): void
    {
        $filename = sprintf(
            '%s_%s.dek',
            $user->getEmail(), // Using email instead of username
            bin2hex(random_bytes(8))
        );
        
        $filepath = $this->uploadPath . '/' . $filename;
        $uploadedFile->moveTo($filepath);
        
        $code = file_get_contents($filepath);
        
        $collection = new UserCollection(
            name: $name,
            code: $code,
            user: $user
        );
        
        $this->collectionRepository->save($collection);
    }
}
