<?php

declare(strict_types=1);

namespace App\Handler\Admin;

use App\Client\MtgJsonClientInterface;
use App\Entity\MtgoItem;
use App\Repository\CardSetRepository;
use App\Repository\MtgoItemRepository;
use DateTimeImmutable;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ImportCardsFromSetHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly MtgoItemRepository $mtgoItemRepository,
        private readonly CardSetRepository $cardSetRepository,
        private readonly MtgJsonClientInterface $mtgJsonClient
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $setCode = $request->getAttribute('code');
        $cardSet = $this->cardSetRepository->findOneByCode($setCode);

        if (! $cardSet) {
            // Handle case when set is not found
            return new RedirectResponse("/admin/mtgo-sets");
        }

        // Get the set data from MTG JSON
        $setData = $this->mtgJsonClient->getSet($setCode);

        $cardSet->setLastImportItemsAt(new DateTimeImmutable());

        $idList = ['mtgoId', 'mtgoFoilId'];
        foreach ($setData as $cardData) {
            foreach ($idList as $idName) {
                if (! isset($cardData[$idName]) || $cardData[$idName] === null) {
                    continue;
                }
                $id = (int) $cardData[$idName];
                // Check if item already exists
                $existingItem = $this->mtgoItemRepository->find($id);

                if (! $existingItem) {
                    // Create new MtgoItem
                    $mtgoItem = new MtgoItem(
                        id: $id,
                        name: $cardData['name'],
                        cardSet: $cardSet,
                        price: 0.0, // Default price, can be updated later
                        collectorNumber: $cardData['number']
                    );

                    $this->mtgoItemRepository->save($mtgoItem, false);
                }
            }
        }
        $this->cardSetRepository->save($cardSet,false);
        $this->mtgoItemRepository->flush();

        return new JsonResponse([
            'status'  => 'success',
            'message' => 'Cards imported successfully',
        ]);
    }
}
