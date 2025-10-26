<?php

declare(strict_types=1);

namespace App\Dto;

final class OrderItemDto
{
    public function __construct(
        public readonly int $id,
        public readonly int $mtgoItemId,
        public readonly string $mtgoItemName,
        public readonly int $quantity
    ) {
    }
}
