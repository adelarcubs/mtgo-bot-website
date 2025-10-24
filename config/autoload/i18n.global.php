<?php

declare(strict_types=1);

return [
    'translator' => [
        'locale' => 'en_US',
    ],
    'i18n'       => [
        'default_locale'            => 'pt_BR',
        'supported_locales'         => [
            'en_US' => 'English',
            'pt_BR' => 'Português do Brasil',
        ],
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => getcwd() . '/data/languages',
                'pattern'  => '%s/LC_MESSAGES/default.mo',
            ],
        ],
    ],
];
