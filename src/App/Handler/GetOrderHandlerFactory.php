<?php

declare(strict_types=1);

namespace App\Handler;

use App\Repository\OrderRepository;
use Psr\Container\ContainerInterface;

class GetOrderHandlerFactory
{
    public function __invoke(ContainerInterface $container): GetOrderHandler
    {
        $orderRepository = $container->get(OrderRepository::class);

        return new GetOrderHandler($orderRepository);
    }
}
