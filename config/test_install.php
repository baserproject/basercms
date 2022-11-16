<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
/**
 * ユニットテスト用のインストール設定ファイル
 */
return [
    'Security.salt' => '0AjCJRJdl3i3caMm5WKDWSLUDXJZQhDSMEmQQF78',
    'Datasources.default' => [
        'className' => 'Cake\\Database\\Connection',
        'driver' => 'Cake\\Database\\Driver\\Mysql',
        'host' => 'bc5-db',
        'port' => '3306',
        'username' => 'root',
        'password' => 'root',
        'database' => 'basercms',
        'prefix' => 'mysite_',
        'schema' => '',
        'encoding' => 'utf8',
        'persistent' => '',
    ],
    'Datasources.test' => [
        'className' => 'Cake\\Database\\Connection',
        'driver' => 'Cake\\Database\\Driver\\Mysql',
        'host' => 'bc5-db',
        'port' => '3306',
        'username' => 'root',
        'password' => 'root',
        'database' => 'test_basercms',
        'prefix' => 'mysite_',
        'schema' => '',
        'encoding' => 'utf8',
        'persistent' => '',
    ],
];
