<?php

return [
    'features' => [
        IsaEken\Proxier\Features\Blocker::class,
        IsaEken\Proxier\Features\ContentWriter::class,
        IsaEken\Proxier\Features\InjectScript::class,
        IsaEken\Proxier\Features\ReplaceUrls::class,
    ],

    'logger' => IsaEken\Proxier\Loggers\NullLogger::class,

    'inject' => [
        'script' => file_get_contents(__DIR__ . '/../resources/script.js'),
    ],

    'content' => [
        'header' => <<<HTML
<!-- isaeken/proxier -->
HTML,
        'footer' => <<<HTML
<!-- isaeken/proxier -->
HTML,
    ],

    'blocker' => [
        'allowed_hosts' => [
            // 'google.com',
            // '*.google.com',
        ],
        'blocked_hosts' => [
            // 'instagram.com',
            // '*.instagram.com',
        ],

        'allowed_methods' => [
            // 'GET',
            // 'POST',
        ],
        'blocked_methods' => [
            // 'PUT',
            // 'PATCH',
            // 'DELETE',
        ],

        'allowed_content_types' => [
            // 'text/html',
            // 'text/plain',
        ],
        'blocked_content_types' => [
            // 'application/json',
            // 'application/xml',
        ],
    ],

    'guzzle' => [
        'timeout' => 10,
        'connect_timeout' => 10,
        'http_errors' => false,
        'verify' => false,
        'cookies' => true,
        'allow_redirects' => [
            'max' => 10,
            'strict' => true,
            'referer' => true,
            'protocols' => ['http', 'https'],
            'track_redirects' => false,
        ],
        'follow_redirects' => [
            'max' => 10,
            'strict' => true,
            'referer' => true,
            'protocols' => ['http', 'https'],
            'track_redirects' => false,
        ],
    ],
];
