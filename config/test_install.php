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
    'Security.salt' => 'af0aa6bb8bad3bf5f9a39e928c645745cc008d67d82ac4edd16ec17e99539725',
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
