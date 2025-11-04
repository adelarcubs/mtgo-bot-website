<?php

declare(strict_types=1);

namespace App\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function ltrim;
use function sprintf;
use function strtoupper;

use const JSON_ERROR_NONE;

class MtgJsonClient implements MtgJsonClientInterface
{
    private const BASE_URI = 'https://mtgjson.com/api/v5/';

    private GuzzleClient $client;

    public function __construct()
    {
        $this->client = new GuzzleClient([
            'base_uri' => self::BASE_URI,
            'headers'  => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            'timeout'  => 30,
        ]);
    }

    public function get(string $endpoint, array $query = []): array
    {
        try {
            $response = $this->client->get($this->normalizeEndpoint($endpoint), [
                'query' => $query,
            ]);

            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            throw new RuntimeException(
                sprintf('MTGJSON API request failed: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    public function post(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->client->post($this->normalizeEndpoint($endpoint), [
                'json' => $data,
            ]);

            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            throw new RuntimeException(
                sprintf('MTGJSON API request failed: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    private function normalizeEndpoint(string $endpoint): string
    {
        return ltrim($endpoint, '/');
    }

    public function getSetList(): array
    {
        return $this->get('SetList.json');
    }

    public function getSet(string $setCode): array
    {
        if (empty($setCode)) {
            throw new InvalidArgumentException('Set code cannot be empty');
        }

        $setCode = strtoupper($setCode);

        return $this->get(sprintf('%s.json', $setCode));
    }

    private function handleResponse(ResponseInterface $response): array
    {
        $content = $response->getBody()->getContents();
        $data    = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                sprintf('Failed to decode JSON response: %s', json_last_error_msg())
            );
        }

        if ($response->getStatusCode() >= 400) {
            $errorMessage = $data['message'] ?? 'Unknown error';
            throw new RuntimeException(
                sprintf('MTGJSON API error: %s', $errorMessage),
                $response->getStatusCode()
            );
        }

        return $data;
    }
}
