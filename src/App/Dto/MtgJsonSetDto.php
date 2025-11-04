<?php

declare(strict_types=1);

namespace App\Dto;

final class MtgJsonSetDto
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
    ) {
    }
}
