<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * paths
 *
 * @checked
 * @unitTest
 * @noTodo
 */

/**
 * Baserディレクトリ名
 */
if (!defined('BASER')) {
    define('BASER', Cake\Core\Plugin::path('BaserCore'));
}
/**
 * Baserコントローラーパス
 */
if (!defined('BASER_CONTROLLERS')) {
    define('BASER_CONTROLLERS', BASER . 'src' . DS . 'Controller' . DS);
}
/**
 * Baserモデルパス
 */
if (!defined('BASER_MODELS')) {
    define('BASER_MODELS', BASER . 'src' . DS . 'Model' . DS);
}
/**
 * Baserビューパス
 */
if (!defined('BASER_VIEWS')) {
    define('BASER_VIEWS', BASER . 'src' . DS . 'View' . DS);
}
/**
 * BaserVendorsパス
 */
if (!defined('BASER_VENDORS')) {
    define('BASER_VENDORS', BASER . 'src' . DS . 'Vendor' . DS);
}
/**
 * Baserコンポーネント
 */

if (!defined('BASER_COMPONENTS')) {
    define('BASER_COMPONENTS', BASER_CONTROLLERS . 'Component' . DS);
}
/**
 * Baserヘルパー
 */
if (!defined('BASER_HELPERS')) {
    define('BASER_HELPERS', BASER_VIEWS . 'Helper' . DS);
}
/**
 * Baserビヘイビア
 */
if (!defined('BASER_BEHAVIORS')) {
    define('BASER_BEHAVIORS', BASER_MODELS . 'Behavior' . DS);
}
/**
 * Baserデータソース
 */
if (!defined('BASER_DATASOURCE')) {
    define('BASER_DATASOURCE', BASER_MODELS . 'Datasource' . DS);
}
/**
 * Baserデータベース
 */
if (!defined('BASER_DATABASE')) {
    define('BASER_DATABASE', BASER_DATASOURCE . 'Database' . DS);
}
/**
 * Baserプラグイン
 */
if (!defined('BASER_PLUGINS')) {
    define('BASER_PLUGINS', ROOT . DS . 'plugins' . DS);
}
/**
 * Baserコンフィグ
 */
if (!defined('BASER_CONFIGS')) {
    define('BASER_CONFIGS', BASER . 'config' . DS);
}
/**
 * BaserLocale
 */
if (!defined('BASER_LOCALES')) {
    define('BASER_LOCALES', BASER . 'src' . DS . 'Locale' . DS);
}
/**
 * BaserEvent
 */
if (!defined('BASER_EVENTS')) {
    define('BASER_EVENTS', BASER . 'src' . DS . 'Event' . DS);
}
/**
 * Baser Utility
 */
if (!defined('BASER_UTILITIES')) {
    define('BASER_UTILITIES', BASER . 'src' . DS . 'Utility' . DS);
}
/**
 * Baser Console
 */
if (!defined('BASER_CONSOLES')) {
    define('BASER_CONSOLES', BASER . 'src' . DS  . 'Console' . DS);
}
/**
 * Baser webroot
 */
if (!defined('BASER_WEBROOT')) {
    define('BASER_WEBROOT', BASER . 'webroot' . DS);
}
/**
 * Baserテーマ
 */
if (!defined('BASER_THEMES')) {
    define('BASER_THEMES', BASER_PLUGINS);
}

