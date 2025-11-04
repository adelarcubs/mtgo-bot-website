<?php

declare(strict_types=1);

namespace App\Client;

interface MtgJsonClientInterface
{
    public function get(string $endpoint, array $query = []): array;

    public function post(string $endpoint, array $data = []): array;

    /**
     * Get a list of all MTG sets
     *
     * @return array Array containing set information
     * @throws RuntimeException If the request fails
     */
    public function getSetList(): array;

    /**
     * Get all cards from a specific set
     *
     * @param string $setCode The set code (e.g., 'ZNR', 'M21')
     * @return array Array containing card information for the specified set
     * @throws RuntimeException If the request fails or set is not found
     * @throws InvalidArgumentException If the set code is empty
     */
    public function getSet(string $setCode): array;
}
