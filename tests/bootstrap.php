<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

use BaserCore\Error\BcException;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorTrap;
use Migrations\TestSuite\Migrator;

/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
require dirname(__DIR__) . '/vendor/autoload.php';

require dirname(__DIR__) . '/config/bootstrap.php';

$_SERVER['PHP_SELF'] = '/';

Configure::write('App.fullBaseUrl', 'http://localhost');

if (empty($_SERVER['HTTP_HOST'])) {
    Configure::write('App.fullBaseUrl', 'http://localhost');
}

// エラー設定 2023/01/12 ryuring
// CakePHP4.4 で PaginatorComponent が非推奨となり、deprecated エラーが発生するため
// 強制的に errorLevel を書き換えて再設定を実行
// /plugins/baser-core/config/setting.php にも設定しているが、ユニットテストの際は、何故か反映されない
// PaginatorComponent の移行が完了できれば削除可
Configure::write('Error.errorLevel', E_ALL & ~E_USER_DEPRECATED);
(new ErrorTrap(Configure::read('Error')))->register();

// DB設定読み込み 2023/01/12 ryuring
// 通常は、/plugins/baser-core/config/bootstrap.php で設定しているが、
// ユニットテストの際、Migrator の実行前に設定が必要なためここで設定
ConnectionManager::drop('default');
ConnectionManager::drop('test');
Configure::load('install');
ConnectionManager::setConfig(Configure::consume('Datasources'));

// DebugKit skips settings these connection config if PHP SAPI is CLI / PHPDBG.
// But since PagesControllerTest is run with debug enabled and DebugKit is loaded
// in application, without setting up these config DebugKit errors out.
ConnectionManager::setConfig('test_debug_kit', [
    'className' => 'Cake\Database\Connection',
    'driver' => 'Cake\Database\Driver\Sqlite',
    'database' => TMP . 'debug_kit.sqlite',
    'encoding' => 'utf8',
    'cacheMetadata' => true,
    'quoteIdentifiers' => false,
]);

ConnectionManager::alias('test_debug_kit', 'debug_kit');

// Fixate sessionid early on, as php7.2+
// does not allow the sessionid to be set after stdout
// has been written to.
session_id('cli');

// ユニットテストを実行する前の事前確認
// GitHubActions で実行する場合は、bin/cake setup test にて、自動的に事前準備をしている
if(!filter_var(env('USE_API'), FILTER_VALIDATE_BOOLEAN)) {
    throw new BcException(__d('baser', 'ユニットテストを実行する際は、.env にて USE_API を true に設定してください。'));
} elseif(!filter_var(env('USE_API'), FILTER_VALIDATE_BOOLEAN)) {
    throw new BcException(__d('baser', 'ユニットテストを実行する際は、.env にて debug を true に設定してください。'));
}

// Use migrations to build test database schema.
//
// Will rebuild the database if the migration state differs
// from the migration history in files.
//
// If you are not using CakePHP's migrations you can
// hook into your migration tool of choice here or
// load schema from a SQL dump file with
// use Cake\TestSuite\Fixture\SchemaLoader;
// (new SchemaLoader())->loadSqlFiles('./tests/schema.sql', 'test');
(new Migrator())->runMany([
    ['plugin' => 'BaserCore'],
    ['plugin' => 'BcBlog'],
    ['plugin' => 'BcSearchIndex'],
    ['plugin' => 'BcContentLink'],
    ['plugin' => 'BcMail'],
    ['plugin' => 'BcWidgetArea'],
    ['plugin' => 'BcThemeConfig'],
    ['plugin' => 'BcThemeFile'],
]);
