<?php
/* SVN FILE: $Id$ */
/**
 * プラグインフックコンポーネント
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
 * @package			baser.controllers.components
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class PluginHookComponent extends Object {
/**
 * プラグインフックオブジェクト
 * @var		array
 * @access	public
 */
	var $pluginHooks = array();
/**
 * 登録済プラグインフック
 * @var		array
 * @access	public
 */
	var $registerHooks = array();
/**
 * initialize
 * @param Controller $controller
 */
	function initialize(&$controller) {

		/* 未インストール・インストール中の場合はすぐリターン */
		if (!file_exists(CONFIGS.'database.php')) {
			return;
		} else {
			require_once(CONFIGS.'database.php');
			$dbConfig = new DATABASE_CONFIG();
			if(!$dbConfig->baser['driver']) return;
		}
		
		if(!empty($controller->enablePlugins)) {
			$plugins = $controller->enablePlugins;
		}else {
			$plugins = array();
			// エラーの際も呼び出される事があるので、テーブルが実際に存在するかチェックする
			$db =& ConnectionManager::getDataSource('baser');
			if ($db->isInterfaceSupported('listSources')) {
				$sources = $db->listSources();
				if (!is_array($sources) || in_array(strtolower($db->config['prefix'] . 'plugins'), array_map('strtolower', $sources))) {
					/* DBに登録されているものだけに変更した */
					$Plugin =& ClassRegistry::init('Plugin','Model');
					$plugins = $Plugin->find('all',array('conditions'=>array('status'=>true)));
					$controller->enablePlugins = $plugins = Set::extract('/Plugin/name',$plugins);
				}
			}
		}

		/* プラグインフックコンポーネントが実際に存在するかチェックしてふるいにかける */
		$pluginHooks = array();
		foreach($plugins as $plugin) {
			$pluginName = Inflector::camelize($plugin);
			if(App::import('Component',$pluginName.'.'.$pluginName.'Hook')) {
				$pluginHooks[] = $pluginName;
			}
		}

		/* プラグインフックを初期化 */
		foreach($pluginHooks as $pluginName) {
			
			$className = $pluginName.'HookComponent';
			$this->pluginHooks[$pluginName] =& new $className();

			// 各プラグインの関数をフックに登録する
			if(isset($this->pluginHooks[$pluginName]->registerHooks)){
				foreach ($this->pluginHooks[$pluginName]->registerHooks as $hookName){
					$this->registerHook($hookName, $pluginName);
				}
			}
			
		}

		/* initialize のフックを実行 */
		$this->executeHook('initialize', $controller);

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
 * プラグインフックを実行する
 * @param	string	$hookName
 * @param	mixed
 * @return
 */
	function executeHook($hookName){
		
		$args = func_get_args();
		unset($args[0]);
		if($this->registerHooks && isset($this->registerHooks[$hookName])){
			foreach($this->registerHooks[$hookName] as $key => $pluginName) {
				call_user_func_array(array(&$this->pluginHooks[$pluginName],$hookName), $args);
			}
		}
		
	}
/**
 * startup
 * @param Controller $controller
 */
	function startup(&$controller) {
		$this->executeHook('startup',$controller);
	}
/**
 * beforeFilter
 * @param Controller $controller
 */
	function beforeFilter(&$controller) {
		$this->executeHook('beforeFilter',$controller);
	}
/**
 * beforeRender
 * @param Controller $controller
 */
	function beforeRender(&$controller) {
		$this->executeHook('beforeRender',$controller);
	}
/**
 * beforeRedirect
 * @param Controller $controller
 */
	function beforeRedirect(&$controller, $url, $status = null, $exit = true) {
		$this->executeHook('beforeRedirect', $controller, $url, $status, $exit);
	}
/**
 * shutdown
 * @param Controller $controller
 */
	function shutdown(&$controller) {
		$this->executeHook('shutdown', $controller);
	}
}
?>