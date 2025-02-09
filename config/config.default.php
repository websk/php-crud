<?php
$path_to_php_crud_project = '/var/www';

return [
    'settings' => [
        'displayErrorDetails' => false,
        'cache' => [
            'engine' => \WebSK\Cache\Engines\Memcached::class,
            'cache_key_prefix' => 'websk_crud',
            'servers' => [
                [
                    'host' => 'memcached',
                    'port' => 11211
                ]
            ]
        ],
        'db' => [
            'db_demo_crud' => [
                'host' => 'mysql',
                'db_name' => 'db_demo_crud',
                'user' => 'root',
                'password' => 'root',
                'dump_file_path' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dumps' . DIRECTORY_SEPARATOR . 'db_demo_crud.sql'
            ]
        ],
        'layout' => [
            'main' => $path_to_php_crud_project . '/views/layouts/layout.main.tpl.php'
        ],
        'log_path' => $path_to_php_crud_project . '/log',
        'tmp_path' => $path_to_php_crud_project . '/tmp',
        'storages' => [
            'files' => [
                'adapter' => 'local',
                'root_path' => $path_to_php_crud_project . '/public/files',
                'url_path' => '/files',
                'allowed_extensions' => ['gif', 'jpeg', 'jpg', 'png', 'pdf', 'csv'],
                'allowed_types' => ['image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/x-png', 'image/png', 'application/pdf', 'application/x-pdf', 'text/csv'],
            ]
        ],
        'site_domain' => 'https://php-crud.devbox',
        'site_full_path' => $path_to_php_crud_project,
        'site_name' => 'PHP CRUD Demo',
        'site_title' => 'WebSK. PHP CRUD Demo',
        'site_email' => 'support@websk.ru'
    ],
];
