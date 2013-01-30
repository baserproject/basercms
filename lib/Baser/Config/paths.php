<?php

/* SVN FILE: $Id$ */
/**
 * パス定義
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.config
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
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
 * Baser TestSuite
 */
define('BASER_LIBS', BASER . 'Lib' . DS);
/**
 * Baser TestCase
 */
define('BASER_TEST_CASES', BASER . 'Test' . DS . 'Case');
/**
 * Baser Console
 */
define('BASER_CONSOLES', BASER . 'Console' . DS);
/**
 * Baserテーマ 
 */
if (is_dir(WWW_ROOT . 'themed')) {
	define('BASER_THEMES', WWW_ROOT . 'themed' . DS);
} elseif (is_dir(ROOT . DS . 'theme')) {
	define('BASER_THEMES', ROOT . DS . 'theme' . DS);
}
