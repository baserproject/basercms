<?php
/* SVN FILE: $Id$ */
/**
 * パス定義
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
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
	define('BASER',ROOT.DS.'baser'.DS);
/**
 * Baserコントローラーパス
 */
	define('BASER_CONTROLLERS',BASER.'controllers'.DS);
/**
 * Baserモデルパス
 */
	define('BASER_MODELS',BASER.'models'.DS);
/**
 * Baserビューパス
 */
	define('BASER_VIEWS',BASER.'views'.DS);
/**
 * BaserVendorsパス
 */
	define('BASER_VENDORS',BASER.'vendors'.DS);
/**
 * Baserコンポーネント
 */
	define('BASER_COMPONENTS',BASER_CONTROLLERS.'components'.DS);
/**
 * Baserヘルパー
 */
	define('BASER_HELPERS',BASER_VIEWS.'helpers'.DS);
/**
 * Baserビヘイビア
 */
	define('BASER_BEHAVIORS',BASER_MODELS.'behaviors'.DS);
/**
 * Baserデータソース
 */
	define('BASER_DBO',BASER_MODELS.'datasources'.DS.'dbo'.DS);
/**
 * Baserプラグイン
 */
	define('BASER_PLUGINS',BASER.'plugins'.DS);
/**
 * Baserコンフィグ
 */
	define('BASER_CONFIGS', BASER.'config'.DS);
/**
 * BaserLocale
 */
	define('BASER_LOCALES',BASER.'locale'.DS);
/**
 * Baserテーマ 
 */
	if(is_dir(WWW_ROOT.'themed')) {
		define('BASER_THEMES', WWW_ROOT.'themed'.DS);
	} elseif(is_dir(ROOT.DS.'themed')) {
		define('BASER_THEMES', ROOT.DS.'themed'.DS);
	}
