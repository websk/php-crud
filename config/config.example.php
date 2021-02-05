<?php
$default_config = require_once __DIR__ . '/config.default.php';

$www_dir = '/var/www';

$config = [
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'db_demo_crud' => [
                'host' => '127.0.0.1',
                'db_name' => 'db_demo_crud',
                'user' => 'root',
                'password' => 'root',
            ]
        ],
        'layout' => [
            'main' => $www_dir . '/php-crud/views/layouts/layout.main.tpl.php'
        ],
        'log_path' => $www_dir . '/log',
        'tmp_path' => $www_dir . '/tmp',
        'storages' => [
            'files' => [
                'adapter' => 'local',
                'root_path' => $www_dir . '/php-crud/public/files',
                'url_path' => '/files',
                'allowed_extensions' => ['gif', 'jpeg', 'jpg', 'png', 'pdf', 'csv'],
                'allowed_types' => ['image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/x-png', 'image/png', 'application/pdf', 'application/x-pdf', 'text/csv'],
            ]
        ],
        'site_full_path' => $www_dir . '/php-crud',
    ],
];

return array_replace_recursive($default_config, $config);