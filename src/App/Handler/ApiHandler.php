<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ApiHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $token = $request->getAttribute('token');
        
        return new JsonResponse([
            'status' => 'success',
            'message' => 'API request successful',
            'token_info' => [
                'id' => $token['id'] ?? null,
                'scopes' => $token['scopes'] ?? [],
                'description' => $token['description'] ?? ''
            ]
        ]);
    }
}
