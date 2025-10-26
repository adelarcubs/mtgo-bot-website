<?php

declare(strict_types=1);

namespace App\Dto;

final class OrderDto
{
    /**
     * @param OrderItemDto[] $items
     */
    public function __construct(
        public readonly int $id,
        public readonly string $createdAt,
        public readonly int $userId,
        public readonly array $items
    ) {
    }
}
