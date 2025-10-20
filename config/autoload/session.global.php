<?php

declare(strict_types=1);

return [
    'session' => [
        'persistent' => true,
        'cache_expire' => 180, // 3 hours
        'cookie_lifetime' => 0, // Until browser is closed
        'cookie_path' => '/',
        'cookie_httponly' => true,
        'cookie_secure' => false, // Set to true in production with HTTPS
        'cookie_samesite' => 'Lax',
        'name' => 'MTGO_SESSION',
        'gc_maxlifetime' => 10800, // 3 hours in seconds
    ],
    'dependencies' => [
        'factories' => [
            \Mezzio\Session\SessionPersistenceInterface::class => \Mezzio\Session\Ext\PhpSessionPersistenceFactory::class,
        ],
    ],
];
