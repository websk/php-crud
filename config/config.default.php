<?php

return [
    'settings' => [
        'displayErrorDetails' => false,
        'cache' => [
            'engine' => \WebSK\Cache\Engines\Memcache::class,
            'cache_key_prefix' => 'phpcrud',
            'servers' => [
                [
                    'host' => 'localhost',
                    'port' => 11211
                ]
            ]
        ],
        'db' => [
            'db_demo_crud' => [
                'host' => 'localhost',
                'db_name' => 'db_demo_crud',
                'user' => 'root',
                'password' => 'root',
            ]
        ],
        'layout' => [
            'main' => '/var/www/php-crud/views/layouts/layout.main.tpl.php'
        ],
        'log_path' => '/var/www/log',
        'tmp_path' => '/var/www/tmp',
        'storages' => [
            'files' => [
                'adapter' => 'local',
                'root_path' => '/var/www/php-crud/public/files',
                'url_path' => '/files',
                'allowed_extension' => ['gif', 'jpeg', 'jpg', 'png', 'pdf', 'csv'],
                'allowed_types' => ['image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/x-png', 'image/png', 'application/pdf', 'application/x-pdf', 'text/csv'],
            ]
        ],
        'site_domain' => 'http://localhost',
        'site_full_path' => '/var/www/php-crud',
        'site_name' => 'PHP CRUD Demo',
        'site_title' => 'WebSK. PHP CRUD Demo',
        'site_email' => 'support@websk.ru'
    ],
];
