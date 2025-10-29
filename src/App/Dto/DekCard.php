<?php

declare(strict_types=1);

namespace App\Dto;

final class DekCard
{
    public function __construct(
        public readonly int $mtgoItemId,
        public readonly int $quantity,
        public readonly string $name
    ) {
    }
}
