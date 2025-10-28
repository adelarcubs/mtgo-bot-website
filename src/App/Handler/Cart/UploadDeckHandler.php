<?php

declare(strict_types=1);

namespace App\Handler\Cart;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\MtgoItem;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\UploadedFile;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

use function array_map;
use function implode;
use function libxml_clear_errors;
use function libxml_get_errors;
use function libxml_use_internal_errors;
use function pathinfo;
use function simplexml_load_string;
use function strtolower;

use const PATHINFO_EXTENSION;
use const UPLOAD_ERR_OK;

class UploadDeckHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $template,
        private EntityManagerInterface $entityManager,
        private CartRepository $cartRepository,
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
                            // Get or create cart for the current user
                            $userId = $request->getAttribute('user_id'); // Assuming user ID is stored in request attributes
                            $cart   = $this->cartRepository->findOneBy(['userId' => $userId]) ?? new Cart($userId);

                            // Process the .dek file and get cart items
                            $deckContents = (string) $uploadedFile->getStream();
                            $cartItems    = $this->parseDeckFile($deckContents, $cart);
                            $this->cartRepository->clearCartItems($cart);

                            // Add items to cart
                            foreach ($cartItems as $cartItem) {
                                $cart->addCartItem($cartItem);
                            }

                            // Save cart
                            $this->entityManager->persist($cart);
                            $this->entityManager->flush();

                            $success = true;
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

    /**
     * Parse the .dek XML file contents and return an array of CartItem objects
     *
     * @param string $contents The XML content of the .dek file
     * @param Cart $cart The cart to associate with the items
     * @return CartItem[] Array of CartItem objects
     * @throws RuntimeException If the XML is invalid or missing required data
     */
    private function parseDeckFile(string $contents, Cart $cart): array
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($contents);

        if ($xml === false) {
            $errors        = libxml_get_errors();
            $errorMessages = array_map(fn($error) => $error->message, $errors);
            libxml_clear_errors();
            throw new RuntimeException('Invalid XML: ' . implode(', ', $errorMessages));
        }

        $cartItems     = [];
        $entityManager = $this->entityManager;

        // Handle direct Cards elements under Deck
        foreach ($xml->Cards as $card) {
            $attributes = $card->attributes();
            $quantity   = (int) $attributes->Quantity;
            $mtgoItemId = (int) $attributes->CatID;

            // Find or create MtgoItem
            $mtgoItem = $entityManager->getRepository(MtgoItem::class)->find($mtgoItemId);

            if ($mtgoItem) {
                // Create CartItem with the provided cart
                $cartItems[] = new CartItem(
                    cart: $cart,
                    mtgoItem: $mtgoItem,
                    quantity: $quantity
                );
            }
        }

        return $cartItems;
    }
}
