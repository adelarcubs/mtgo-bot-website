<?php

declare(strict_types=1);

namespace App\Handler\Cart;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\CartRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function number_format;

class GetCartHandler implements RequestHandlerInterface
{
    public function __construct(
        private CartRepository $cartRepository,
        private TemplateRendererInterface $template
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Get user ID from session or token
        $userId = $request->getAttribute('user_id');
        // if ($userId === null) {
        // // Redirect to login if user is not authenticated
        // return new HtmlResponse($this->template->render('error::403', [
        // 'message' => 'You must be logged in to view your cart.'
        // ]), 403);
        // }

        $cart = $this->cartRepository->findOneBy(['userId' => $userId]);

        $data = [
            'cart' => $cart ? $this->formatCart($cart) : [
                'id'         => null,
                'items'      => [],
                'total'      => 0,
                'totalItems' => 0,
            ],
        ];

        return new HtmlResponse(
            $this->template->render('app::cart/index', $data)
        );
    }

    private function formatCart(Cart $cart): array
    {
        $items      = [];
        $total      = 0.0;
        $totalItems = 0;

        /** @var CartItem $item */
        foreach ($cart->getCartItems() as $item) {
            $mtgoItem  = $item->getMtgoItem();
            $itemPrice = $mtgoItem->getPrice();
            $itemTotal = $item->getQuantity() * $itemPrice;

            $items[] = [
                'id'              => $item->getId(),
                'mtgoItemId'      => $mtgoItem->getId(),
                'name'            => $mtgoItem->getName(),
                'quantity'        => $item->getQuantity(),
                'price'           => $itemPrice,
                'total'           => $itemTotal,
                'setCode'         => $mtgoItem->getSetCode(),
                'collectorNumber' => $mtgoItem->getCollectorNumber(),
            ];

            $total      += $itemTotal;
            $totalItems += $item->getQuantity();
        }

        return [
            'id'             => $cart->getId(),
            'createdAt'      => $cart->getCreatedAt(),
            'updatedAt'      => $cart->getUpdatedAt(),
            'items'          => $items,
            'total'          => $total,
            'totalItems'     => $totalItems,
            'formattedTotal' => number_format($total, 2, ',', '.'),
        ];
    }
}
