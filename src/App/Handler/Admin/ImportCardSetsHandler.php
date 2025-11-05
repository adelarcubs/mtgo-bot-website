<?php

declare(strict_types=1);

namespace App\Handler\Admin;

use App\Client\MtgJsonClientInterface;
use App\Entity\CardSet;
use App\Repository\CardSetRepository;
use DateTimeImmutable;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ImportCardSetsHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly CardSetRepository $cardSetRepository,
        private readonly MtgJsonClientInterface $mtgJsonClient
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /*
        if ($request->getMethod() === 'POST') {
            // Handle form submission and import logic here
            // You'll need to implement the actual import logic
            $success = true; // Replace with actual import logic
        }
        */

        $setList = $this->mtgJsonClient->getSetList();
        $now = new DateTimeImmutable();
        foreach ($setList as $set) {
            $loadedSet = $this->cardSetRepository->findOneByCode($set->code);
            if (! $loadedSet) {
                $loadedSet = new CardSet($set->name, $set->code);
            }
            $loadedSet->setLastImportAt($now);
            $this->cardSetRepository->save($loadedSet, false);
        }

        $this->cardSetRepository->flush();

        return new JsonResponse([
            'status'  => 'success',
            'message' => 'Sets imported successfully',
        ]);
    }
}
