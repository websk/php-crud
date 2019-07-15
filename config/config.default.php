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
            'main' => '/var/www/php-auth/views/layouts/layout.main.tpl.php'
        ],
        'log_path' => '/var/www/log',
        'tmp_path' => '/var/www/tmp',
        'files_data_path' => '/var/www/php-crud/public/files',
        'site_domain' => 'http://localhost',
        'site_full_path' => '/var/www/php-crud',
        'site_name' => 'PHP CRUD Demo',
        'site_title' => 'WebSK. PHP CRUD Demo',
        'site_email' => 'support@websk.ru'
    ],
];
