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
    define('BASER_CONTROLLERS', BASER . 'Controller' . DS);
}
/**
 * Baserモデルパス
 */
if (!defined('BASER_MODELS')) {
    define('BASER_MODELS', BASER . 'Model' . DS);
}
/**
 * Baserビューパス
 */
if (!defined('BASER_VIEWS')) {
    define('BASER_VIEWS', BASER . 'View' . DS);
}
/**
 * BaserVendorsパス
 */
if (!defined('BASER_VENDORS')) {
    define('BASER_VENDORS', BASER . 'Vendor' . DS);
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
    define('BASER_PLUGINS', BASER . 'Plugin' . DS);
}
/**
 * Baserコンフィグ
 */
if (!defined('BASER_CONFIGS')) {
    define('BASER_CONFIGS', BASER . 'Config' . DS);
}
/**
 * BaserLocale
 */
if (!defined('BASER_LOCALES')) {
    define('BASER_LOCALES', BASER . 'Locale' . DS);
}
/**
 * BaserEvent
 */
if (!defined('BASER_EVENTS')) {
    define('BASER_EVENTS', BASER . 'Event' . DS);
}
/**
 * Baser Libs
 */
if (!defined('BASER_LIBS')) {
    define('BASER_LIBS', BASER . 'Lib' . DS);
}
/**
 * Baser TestSuite
 */
if (!defined('BASER_TEST_SUITE')) {
    define('BASER_TEST_SUITE', BASER_LIBS . 'TestSuite' . DS);
}
/**
 * Baser TestCase
 */
if (!defined('BASER_TEST_CASES')) {
    define('BASER_TEST_CASES', BASER . 'Test' . DS . 'Case');
}
/**
 * Baser Console
 */
if (!defined('BASER_CONSOLES')) {
    define('BASER_CONSOLES', BASER . 'Console' . DS);
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
    if (is_dir(WWW_ROOT . 'theme')) {
        define('BASER_THEMES', WWW_ROOT . 'theme' . DS);
    } elseif (is_dir(ROOT . DS . 'theme')) {
        define('BASER_THEMES', ROOT . DS . 'theme' . DS);
    }
}

