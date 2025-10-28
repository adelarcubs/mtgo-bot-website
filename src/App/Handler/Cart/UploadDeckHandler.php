<?php

declare(strict_types=1);

namespace App\Handler\Cart;

use Exception;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\UploadedFile;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function pathinfo;
use function strtolower;

use const PATHINFO_EXTENSION;
use const UPLOAD_ERR_OK;

class UploadDeckHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $template,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $error   = null;
        $success = false;

        if ($request->getMethod() === 'POST') {
            $files = $request->getUploadedFiles();

            if (! empty($files['deckFile'])) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $files['deckFile'];

                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    $fileName = $uploadedFile->getClientFilename();
                    $fileExt  = pathinfo($fileName, PATHINFO_EXTENSION);

                    if (strtolower($fileExt) === 'dek') {
                        try {
                            // Process the .dek file here
                            $deckContents = (string) $uploadedFile->getStream();

                            // TODO: Add your deck processing logic here
                            // For now, we'll just set success to true
                            $success = true;

                            // You can parse the deck contents and pass them to the template
                            // $deckData = $this->parseDeckFile($deckContents);
                        } catch (Exception $e) {
                            $error = 'Error processing deck file: ' . $e->getMessage();
                        }
                    } else {
                        $error = 'Invalid file type. Please upload a .dek file.';
                    }
                } else {
                    $error = 'Error uploading file. Please try again.';
                }
            } else {
                $error = 'No file uploaded.';
            }
        }

        return new HtmlResponse($this->template->render('app::cart/upload', [
            'error'   => $error,
            'success' => $success,
        ]));
    }

    // TODO: Implement deck file parsing logic
    /*
    private function parseDeckFile(string $contents): array
    {
        // Parse the .dek file contents and return structured data
        return [];
    }
    */
}
