<?php

declare(strict_types=1);

namespace App\Handler;

use App\Repository\RentedCardRepository;
use App\Repository\UserRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class MyRentHandlerFactory
{
    public function __invoke(ContainerInterface $container): MyRentHandler
    {
        return new MyRentHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(RentedCardRepository::class),
            $container->get(UserRepository::class)
        );
    }
}
