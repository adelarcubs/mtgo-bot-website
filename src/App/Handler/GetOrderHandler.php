<?php

declare(strict_types=1);

namespace App\Handler;

use App\Dto\OrderDto;
use App\Dto\OrderItemDto;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetOrderHandler implements RequestHandlerInterface
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $orderId = (int) $request->getAttribute('id');

        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }

        $orderDto = $this->createOrderDto($order);

        return new JsonResponse($orderDto);
    }

    private function createOrderDto(Order $order): OrderDto
    {
        $items = [];

        foreach ($order->getOrderItems() as $orderItem) {
            $items[] = new OrderItemDto(
                $orderItem->getId(),
                $orderItem->getMtgoItem()->getId(),
                $orderItem->getMtgoItem()->getName(),
                $orderItem->getQuantity()
            );
        }

        return new OrderDto(
            $order->getId(),
            $order->getCreatedAt()->format('c'),
            $order->getUserId(),
            $items
        );
    }
}
