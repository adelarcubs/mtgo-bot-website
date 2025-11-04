<?php

declare(strict_types=1);

namespace App\Handler\Admin;

use App\Entity\CardSet;
use App\Repository\CardSetRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class ListCardSetsHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly CardSetRepository $cardSetRepository
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $cardSets = $this->cardSetRepository->findBy([], ['name' => 'ASC']);

        return new HtmlResponse(
            $this->template->render('app::admin/mtgo-sets/list', [
                'cardSets' => $cardSets,
            ])
        );
    }
}
