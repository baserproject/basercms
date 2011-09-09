<?php
/* SVN FILE: $Id$ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.webroot
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
$fileName = $_SERVER['SCRIPT_FILENAME'];
/**
 * タイムゾーンを設定する
 */
	if(!ini_get('date.timezone')) {
 		ini_set('date.timezone', 'Asia/Tokyo');
 	}
	@putenv("TZ=JST-9");
/**
 * Use the DS to separate the directories in other defines
 */
	if (!defined('DS')) {
		define('DS', DIRECTORY_SEPARATOR);
	}
/**
 * These defines should only be edited if you have cake installed in
 * a directory layout other than the way it is distributed.
 * When using custom settings be sure to use the DS and do not add a trailing DS.
 */

/**
 * The full path to the directory which holds "app", WITHOUT a trailing DS.
 * 
 * 基本的には、cake が配置されているディレクトリをROOTとみなす。
 */
	if (!defined('ROOT')) {
		/* 通常パターン */
		if(@is_dir(dirname(dirname(dirname($fileName))).DS.'cake')){
			define('ROOT', dirname(dirname(dirname($fileName))));
		// app内にcakeを配置
		// チカッパでは、DocumentoRoot のひとつ上の階層にcake を配置していた為、
		// そちらをターゲットとして ROOT を決定した為、うまく動作しなかった。
		/*}elseif(is_dir(dirname(dirname($fileName)).DS.'cake')){		
			define('ROOT', dirname(dirname($fileName)));*/
		
		// WEBROOT配置
		}elseif(is_dir(dirname($fileName).DS.'cake')){
			define('ROOT', dirname($fileName));
		}
	}
/**
 * The actual directory name for the "app".
 * 
 * app ディレクトリは「WEBROOT配置」の絡みがあるので[app]固定とする
 * app ディレクトリの名称を変更する場合は、以下を変更する。
 */
	if (!defined('APP_DIR')) {
		//define('APP_DIR', basename(dirname(dirname($fileName))));
		define('APP_DIR', 'app');
	}
/**
 * The absolute path to the "cake" directory, WITHOUT a trailing DS.
 *
 */
	if (!defined('CAKE_CORE_INCLUDE_PATH')) {
		define('CAKE_CORE_INCLUDE_PATH', ROOT);
	}

/**
 * Editing below this line should NOT be necessary.
 * Change at your own risk.
 *
 */
	if (!defined('WEBROOT_DIR')) {
		define('WEBROOT_DIR', basename(dirname($fileName)));
	}
	if (!defined('WWW_ROOT')) {
		define('WWW_ROOT', dirname($fileName) . DS);
	}
	if (!defined('CORE_PATH')) {
		if (function_exists('ini_set') && ini_set('include_path', CAKE_CORE_INCLUDE_PATH . PATH_SEPARATOR . ROOT . DS . APP_DIR . DS . PATH_SEPARATOR . ini_get('include_path'))) {
			define('APP_PATH', null);
			define('CORE_PATH', null);
		} else {
			define('APP_PATH', ROOT . DS . APP_DIR . DS);
			define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
		}
	}
	if (!include(CORE_PATH . 'cake' . DS . 'bootstrap.php')) {
		trigger_error("CakePHP core could not be found.  Check the value of CAKE_CORE_INCLUDE_PATH in APP/webroot/index.php.  It should point to the directory containing your " . DS . "cake core directory and your " . DS . "vendors root directory.", E_USER_ERROR);
	}
	if (isset($_GET['url']) && $_GET['url'] === 'favicon.ico') {
		return;
	} else {
		$Dispatcher = new Dispatcher();
		$Dispatcher->dispatch($url);
	}
	if (Configure::read() > 0) {
		echo "<!-- " . round(getMicrotime() - $TIME_START, 4) . "s -->";
	}
?>