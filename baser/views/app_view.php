<?php
/* SVN FILE: $Id$ */
/**
 * view 拡張クラス
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Core','Theme');
/**
 * view 拡張クラス
 *
 * @package			baser.views
 */
class AppView extends ThemeView {
/**
 * List of variables to collect from the associated controller
 *
 * @var array
 * @access protected
 */
	var $__passedVars = array(
			'viewVars', 'action', 'autoLayout', 'autoRender', 'ext', 'base', 'webroot',
			'helpers', 'here', 'layout', 'name', 'pageTitle', 'layoutPath', 'viewPath',
			'params', 'data', 'plugin', 'passedArgs', 'cacheAction', 'subDir'
	);
/**
 * テンプレートのファイル名を取得する
 * プレフィックスが設定されている場合は、プレフィックスを除外する
 * @param	string	$name
 * @return	string	$fileName
 * @access	protected
 */
	function _getViewFileName($name = null) {

		if(!$name && isset($this->params['prefix'])) {
			$prefix = $this->params['prefix'];
			if(preg_match('/^'.$prefix.'_/', $this->action)) {
				$name = str_replace($prefix.'_','',$this->action);
			} elseif(preg_match('/^admin_/', $this->action)) {
				// プレフィックスをadminとしてすり替え
				$name = str_replace('admin_','',$this->action);
			}
		}
		if($this->name == 'CakeError' && $this->viewPath == 'errors') {
			// CakeErrorの場合はサブフォルダを除外
			$subDir = $this->subDir;
			$this->subDir = '';
			$fileName = parent::_getViewFileName($name);
			$this->subDir = $subDir;
			return $fileName;
		}else {
			return parent::_getViewFileName($name);
		}

	}
/**
 * Return all possible paths to find view files in order
 *
 * @param string $plugin
 * @return array paths
 * @access private
 */
	function _paths($plugin = null, $cached = true) {
		$paths = $this->__paths($plugin, $cached);

		if (!empty($this->theme)) {
			$count = count($paths);
			for ($i = 0; $i < $count; $i++) {
				// >>> CUSTOMIZE MODIFY 2011/03/24 ryuring
				// プラグインパスにテーマのパスを追加した為、
				// テーマのパスをさらにテーマのパスに整形しないように調整
				//$themePaths[] = $paths[$i] . 'themed'. DS . $this->theme . DS;
				// ---
				if(strpos($paths[$i],'themed') === false) {
					$themePaths[] = $paths[$i] . 'themed'. DS . $this->theme . DS;
				}
				// <<<
			}
			$paths = array_merge($themePaths, $paths);
		}

		if (empty($this->__paths)) {
			$this->__paths = $paths;
		}

		return $paths;
	}
/**
 * Return all possible paths to find view files in order
 * 
 * ※ _paths より直接呼び出されるようにする為だけに、Viewクラスより中身をコピー
 * 
 * @param string $plugin
 * @return array paths
 * @access protected
 */
	function __paths($plugin = null, $cached = true) {
		if ($plugin === null && $cached === true && !empty($this->__paths)) {
			return $this->__paths;
		}
		$paths = array();
		$viewPaths = Configure::read('viewPaths');
		$corePaths = array_flip(Configure::corePaths('view'));

		if (!empty($plugin)) {
			$count = count($viewPaths);
			for ($i = 0; $i < $count; $i++) {
				if (!isset($corePaths[$viewPaths[$i]])) {
					$paths[] = $viewPaths[$i] . 'plugins' . DS . $plugin . DS;
				}
			}
			$pluginPaths = Configure::read('pluginPaths');
			$count = count($pluginPaths);

			for ($i = 0; $i < $count; $i++) {
				$paths[] = $pluginPaths[$i] . $plugin . DS . 'views' . DS;
			}
		}
		$paths = array_merge($paths, $viewPaths);

		if (empty($this->__paths)) {
			$this->__paths = $paths;
		}
		return $paths;
	}
/**
 * フック処理を実行する
 *
 * @param	string	$out
 * @return	mixed
 */
	function executeHook($hook, $out) {

		return $this->loaded['pluginHook']->{$hook}($out);

	}
	
}
?>