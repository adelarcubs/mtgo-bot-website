<?php

declare(strict_types=1);

return [
    'authentication' => [
        'tokens'        => [
            // Development token - in production, use environment variables
            ':dev-token-123' => [
                'description' => 'Development Token',
                'scopes'      => ['api:read', 'api:write'],
                'expires'     => null, // null means never expires
            ],
            // Add more tokens as needed
        ],
        'realm'         => 'API',
        'login_url'     => '/login',
        'public_routes' => [
            '/',
            '/login',
            '/register',
            '/logout',
            '/our-bots',
        ],
    ],
];
