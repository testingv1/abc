<?php
require 'app.php';
return [
    'paths' => ['migrations' => 'src/migrations'],
    'migration_base_class' => 'App\Libs\Migration',
    'environments' => [
        'default_migration_table' => 'migrations',
        'dev' => [
            'adapter' => DB_DRIVER,
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASSWORD,
            'port' => DB_PORT
        ]
    ]
];
