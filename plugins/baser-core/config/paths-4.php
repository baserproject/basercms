<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Config
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Baserディレクトリ名
 */
define('BASER', CORE_PATH . 'Baser' . DS);
/**
 * Baserコントローラーパス
 */
define('BASER_CONTROLLERS', BASER . 'Controller' . DS);
/**
 * Baserモデルパス
 */
define('BASER_MODELS', BASER . 'Model' . DS);
/**
 * Baserビューパス
 */
define('BASER_VIEWS', BASER . 'View' . DS);
/**
 * BaserVendorsパス
 */
define('BASER_VENDORS', BASER . 'Vendor' . DS);
/**
 * Baserコンポーネント
 */
define('BASER_COMPONENTS', BASER_CONTROLLERS . 'Component' . DS);
/**
 * Baserヘルパー
 */
define('BASER_HELPERS', BASER_VIEWS . 'Helper' . DS);
/**
 * Baserビヘイビア
 */
define('BASER_BEHAVIORS', BASER_MODELS . 'Behavior' . DS);
/**
 * Baserデータソース
 */
define('BASER_DATASOURCE', BASER_MODELS . 'Datasource' . DS);
/**
 * Baserデータベース
 */
define('BASER_DATABASE', BASER_DATASOURCE . 'Database' . DS);
/**
 * Baserプラグイン
 */
define('BASER_PLUGINS', BASER . 'Plugin' . DS);
/**
 * Baserコンフィグ
 */
define('BASER_CONFIGS', BASER . 'Config' . DS);
/**
 * BaserLocale
 */
define('BASER_LOCALES', BASER . 'Locale' . DS);
/**
 * BaserEvent
 */
define('BASER_EVENTS', BASER . 'Event' . DS);
/**
 * Baser Libs
 */
define('BASER_LIBS', BASER . 'Lib' . DS);
/**
 * Baser TestSuite
 */
define('BASER_TEST_SUITE', BASER_LIBS . 'TestSuite' . DS);
/**
 * Baser TestCase
 */
define('BASER_TEST_CASES', BASER . 'Test' . DS . 'Case');
/**
 * Baser Console
 */
define('BASER_CONSOLES', BASER . 'Console' . DS);
/**
 * Baser webroot
 */
define('BASER_WEBROOT', BASER . 'webroot' . DS);
/**
 * Baserテーマ
 */
if (is_dir(WWW_ROOT . 'theme')) {
	define('BASER_THEMES', WWW_ROOT . 'theme' . DS);
} elseif (is_dir(ROOT . DS . 'theme')) {
	define('BASER_THEMES', ROOT . DS . 'theme' . DS);
}
