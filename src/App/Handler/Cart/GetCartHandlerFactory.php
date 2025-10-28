<?php

declare(strict_types=1);

namespace App\Handler\Cart;

use App\Repository\CartRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetCartHandlerFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        return new GetCartHandler(
            $container->get(CartRepository::class),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
