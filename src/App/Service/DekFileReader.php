<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\DekCard;
use RuntimeException;
use SimpleXMLElement;

use function file_exists;
use function simplexml_load_file;

class DekFileReader
{
    /**
     * @return array<DekCard>
     */
    public function readDekFile(string $filePath): array
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException("Dek file not found: {$filePath}");
        }

        $xml = simplexml_load_file($filePath);
        if ($xml === false) {
            throw new RuntimeException("Failed to parse dek file: {$filePath}");
        }

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
