<?php
/* SVN FILE: $Id$ */
/**
 * プラグインフックヘルパー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
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
		if(!file_exists(CONFIGS.'database.php')) {
			return;
		}else {
			require_once(CONFIGS.'database.php');
			$dbConfig = new DATABASE_CONFIG();
			if(!$dbConfig->baser['driver']) return;
		}

		$view = ClassRegistry::getObject('View');
		if(!empty($view->enablePlugins)) {
			$plugins = $view->enablePlugins;
		}else {
			$plugins = array();
			// エラーの際も呼び出される事があるので、テーブルが実際に存在するかチェックする
			$db =& ConnectionManager::getDataSource('baser');
			if ($db->isInterfaceSupported('listSources')) {
				$sources = $db->listSources();
				if (!is_array($sources) || in_array(strtolower($db->config['prefix'] . 'plugins'), array_map('strtolower', $sources))) {
					$Plugin =& ClassRegistry::init('Plugin','Model');
					$plugins = $Plugin->find('all',array('fields'=>array('name'), 'conditions'=>array('status'=>true)));
					$plugins = Set::extract('/Plugin/name',$plugins);
				}
			}
		}

		/* プラグインフックコンポーネントが実際に存在するかチェックしてふるいにかける */
		$pluginHooks = array();
		foreach($plugins as $plugin) {
			$pluginName = Inflector::camelize($plugin);
			if(App::import('Helper',$pluginName.'.'.$pluginName.'Hook')) {
				$pluginHooks[] = $pluginName;
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
		// TODO ファイルアップローダーが新しいPluginHookの仕様にバージョンアップしたら
		// afterLayoutも新しい仕様に変更する
		foreach($this->pluginHooks as $key => $pluginHook) {
			if(method_exists($this->pluginHooks[$key],"afterLayout")) {
				$this->pluginHooks[$key]->afterLayout();
			}
		}
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
 * FormEx::create
 * @param	string	$out
 * @return	string
 * @access	public
 */
	function formExCreate($out) {
		return $this->executeHook('formExCreate',$out);
	}
}
?>