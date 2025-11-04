<?php

declare(strict_types=1);

namespace App\Handler\Admin\Factory;

use App\Client\MtgJsonClientInterface;
use App\Handler\Admin\ImportCardsFromSetHandler;
use App\Repository\CardSetRepository;
use App\Repository\MtgoItemRepository;
use Psr\Container\ContainerInterface;

class ImportCardsFromSetHandlerFactory
{
    public function __invoke(ContainerInterface $container): ImportCardsFromSetHandler
    {
        $mtgoItemRepository = $container->get(MtgoItemRepository::class);
        $cardSetRepository  = $container->get(CardSetRepository::class);
        $mtgJsonClient      = $container->get(MtgJsonClientInterface::class);

        return new ImportCardsFromSetHandler(
            $mtgoItemRepository,
            $cardSetRepository,
            $mtgJsonClient
        );
    }
}
