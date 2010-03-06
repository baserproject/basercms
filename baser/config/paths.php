<?php
/* SVN FILE: $Id$ */
/**
 * パス定義
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.config
 * @since			Baser v 0.1.0
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
?>