<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\DekCard;
use RuntimeException;
use SimpleXMLElement;

use function array_map;
use function file_exists;
use function implode;
use function libxml_clear_errors;
use function libxml_get_errors;
use function libxml_use_internal_errors;
use function simplexml_load_file;
use function simplexml_load_string;

class DekFileReader
{
    /**
     * @return array<DekCard>
     */
    public function readDekFileFromPath(string $filePath): array
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException("Dek file not found: {$filePath}");
        }

        $xml = simplexml_load_file($filePath);
        if ($xml === false) {
            throw new RuntimeException("Failed to parse dek file: {$filePath}");
        }
        return $this->readCards($xml);
    }

    public function readDekFileFromStream(string $fileStream): array
    {
                libxml_use_internal_errors(true);
        $xml = simplexml_load_string($fileStream);

        if ($xml === false) {
            $errors        = libxml_get_errors();
            $errorMessages = array_map(fn($error) => $error->message, $errors);
            libxml_clear_errors();
            throw new RuntimeException('Invalid XML: ' . implode(', ', $errorMessages));
        }

        return $this->readCards($xml);
    }

    private function readCards(SimpleXMLElement $xml): array
    {
        $cards = [];

        // Process all cards
        foreach ($xml->xpath('//Cards') as $cardElement) {
            $cards[] = $this->createDekCardFromElement($cardElement);
        }

        return $cards;
    }

    private function createDekCardFromElement(SimpleXMLElement $element): DekCard
    {
        $attributes = $element->attributes();

        return new DekCard(
            mtgoItemId: (int) ($attributes['CatID'] ?? 0),
            quantity: (int) ($attributes['Quantity'] ?? 0),
            name: (string) ($attributes['Name'] ?? 'Unknown')
        );
    }
}
