<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\DeckCard;
use RuntimeException;
use SimpleXMLElement;

use function file_exists;
use function simplexml_load_file;

class DeckFileReader
{
    /**
     * @return array<DeckCard>
     */
    public function readDeckFile(string $filePath): array
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException("Deck file not found: {$filePath}");
        }

        $xml = simplexml_load_file($filePath);
        if ($xml === false) {
            throw new RuntimeException("Failed to parse deck file: {$filePath}");
        }

        $cards = [];

        // Process all cards
        foreach ($xml->xpath('//Cards') as $cardElement) {
            $cards[] = $this->createDeckCardFromElement($cardElement);
        }

        return $cards;
    }

    private function createDeckCardFromElement(SimpleXMLElement $element): DeckCard
    {
        $attributes = $element->attributes();

        return new DeckCard(
            mtgoItemId: (int) ($attributes['CatID'] ?? 0),
            quantity: (int) ($attributes['Quantity'] ?? 0),
            name: (string) ($attributes['Name'] ?? 'Unknown')
        );
    }
}
