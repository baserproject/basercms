<?php
// created by BcInstaller
return [
    'Security.salt' => '7DWZEdlSFH6KH0LSTfXcN2Fl1LJcZTQH9RT3BVcl',
    'Datasources.default' => [
        'className' => 'Cake\\Database\\Connection',
        'driver' => 'Cake\\Database\\Driver\\Mysql',
        'host' => env('DB_HOST', 'cu-db'),
        'port' => '3306',
        'username' => 'catchup',
        'password' => 'catchup55',
        'database' => 'dzero-theme',
        'prefix' => '',
        'schema' => '',
        'encoding' => 'utf8mb4',
        'persistent' => '',
        'log' => filter_var(env('SQL_LOG', false), FILTER_VALIDATE_BOOLEAN)
    ],
    'Datasources.test' => [
        'className' => 'Cake\\Database\\Connection',
        'driver' => 'Cake\\Database\\Driver\\Mysql',
        'host' => 'cu-db',
        'port' => '3306',
        'username' => 'catchup',
        'password' => 'catchup55',
        'database' => 'test_dzero-theme',
        'prefix' => '',
        'schema' => '',
        'encoding' => 'utf8mb4',
        'persistent' => '',
        'log' => filter_var(env('SQL_LOG', false), FILTER_VALIDATE_BOOLEAN)
    ]
];
