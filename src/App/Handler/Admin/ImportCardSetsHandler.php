<?php

declare(strict_types=1);

namespace App\Handler\Admin;

use App\Repository\CardSetRepository;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ImportCardSetsHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly CardSetRepository $cardSetRepository
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
        return new RedirectResponse('/admin/mtgo-sets');
    }
}
