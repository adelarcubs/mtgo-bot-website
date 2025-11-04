<?php

declare(strict_types=1);

namespace App\Factory;

use App\Client\MtgJsonClient;
use Psr\Container\ContainerInterface;

class MtgJsonClientFactory
{
    public function __invoke(ContainerInterface $container): MtgJsonClient
    {
        return new MtgJsonClient();
    }
}
