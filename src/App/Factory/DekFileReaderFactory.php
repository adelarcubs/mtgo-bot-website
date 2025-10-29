<?php

declare(strict_types=1);

namespace App\Factory;

use App\Service\DekFileReader;
use Psr\Container\ContainerInterface;

class DekFileReaderFactory
{
    public function __invoke(ContainerInterface $container): DekFileReader
    {
        return new DekFileReader();
    }
}
