<?php
/* SVN FILE: $Id$ */
/**
 * プラグインフックヘルパー
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
 * @package			baser.views.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class PluginHookHelper extends AppHelper {
/**
 * プラグインフックオブジェクト
 * @var array
 */
	var $pluginHooks = array();
/**
 * 登録済フックメソッド
 * @var	array
 */
	var $registerHooks = array();
/**
 * beforeRender
 * @access	public
 */
	function beforeRender() {

		/* 未インストール・インストール中の場合はすぐリターン */
		if(!isInstalled ()) {
			return;
		}

		$view = ClassRegistry::getObject('View');
		$plugins = Configure::read('Baser.enablePlugins');

		/* プラグインフックコンポーネントが実際に存在するかチェックしてふるいにかける */
		$pluginHooks = array();
		if($plugins) {
			foreach($plugins as $plugin) {
				$pluginName = Inflector::camelize($plugin);
				if(App::import('Helper',$pluginName.'.'.$pluginName.'Hook')) {
					$pluginHooks[] = $pluginName;
				}
			}
		}
		
		/* プラグインフックを初期化 */
		$vars = array('base', 'webroot', 'here', 'params', 'action', 'data', 'themeWeb', 'plugin');
		$c = count($vars);
		foreach($pluginHooks as $key => $pluginName) {

			// 各プラグインのプラグインフックを初期化
			$className=$pluginName.'HookHelper';
			$this->pluginHooks[$pluginName] =& new $className();
			for ($j = 0; $j < $c; $j++) {
				if(isset($view->{$vars[$j]})) {
					$this->pluginHooks[$pluginName]->{$vars[$j]} = $view->{$vars[$j]};
				}
			}

			// 各プラグインの関数をフックに登録する
			if(isset($this->pluginHooks[$pluginName]->registerHooks)){
				foreach ($this->pluginHooks[$pluginName]->registerHooks as $hookName){
					$this->registerHook($hookName, $pluginName);
				}
			}

		}

		/* beforeRenderをフック */
		$this->executeHook('beforeRender');

	}
/**
 * プラグインフックを登録する
 * @param	string	$hookName
 * @param	string	$pluginName
 * @return	void
 * @access	pubic
 */
	function registerHook($hookName, $pluginName){

		if(!isset($this->registerHooks[$hookName])){
			$this->registerHooks[$hookName] = array();
		}

		$this->registerHooks[$hookName][] = $pluginName;

	}
/**
 * フックを実行する
 *
 * @param	string	$hookName
 * @param	string	$out
 * @return	string	$out
 * @access	public
 */
	function executeHook($hookName, $out = null){
		if($this->registerHooks && isset($this->registerHooks[$hookName])){
			foreach($this->registerHooks[$hookName] as $key => $pluginName) {
				$out = $this->pluginHooks[$pluginName]->{$hookName}($out);
			}
		}
		return $out;
	}
/**
 * afterRender
 */
	function afterRender() {
		$this->executeHook('afterRender');
	}
/**
 * beforeLayout
 */
	function beforeLayout() {
		$this->executeHook('beforeLayout');
	}
/**
 * afterLayout
 */
	function afterLayout() {
		$this->executeHook('afterLayout');
	}
/**
 * FormEx::create
 * @param	string	$out
 * @return	string
 * @access	public
 */
	function formExCreate($out) {
		return $this->executeHook('formExCreate', $out);
	}
/**
 * FormEx::end
 * @param	string	$out
 * @return	string
 * @access	public
 */
	function formExEnd($out) {
		return $this->executeHook('formExEnd',$out);
	}
/**
 * Baser::header
 *
 * @param string $out
 * @return string
 */
	function baserHeader($out) {
		return $this->executeHook('baserHeader', $out);
	}
/**
 * Baser::footer
 *
 * @param string $out
 * @return string
 */
	function baserFooter($out) {
		return $this->executeHook('baserFooter', $out);
	}
	
}
?>