<?php
/* SVN FILE: $Id$ */
/**
 * プラグインフックコンポーネント
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers.components
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Core', 'Overloadable');
/**
 * プラグインフックコンポーネント
 *
 * @package			baser.controllers.components
 */
class BcPluginHookComponent extends Overloadable {
/**
 * プラグインフックオブジェクト
 * 
 * @var array
 * @access	public
 */
	var $pluginHooks = array();
/**
 * 登録済プラグインフック
 * 
 * @var array
 * @access	public
 */
	var $registerHooks = array();
/**
 * initialize
 * 
 * @param Controller $controller
 * @return void
 * @access public
 */
	function initialize($controller) {

		/* 未インストール・インストール中の場合はすぐリターン */
		if(!isInstalled ()) {
			return;
		}
		
		$plugins = Configure::read('BcStatus.enablePlugins');

		/* プラグインフックコンポーネントが実際に存在するかチェックしてふるいにかける */
		$pluginHooks = array();
		if($plugins) {
			foreach($plugins as $plugin) {
				$pluginName = Inflector::camelize($plugin);
				if(App::import('Component',$pluginName.'.'.$pluginName.'Hook')) {
					$pluginHooks[] = $pluginName;
				}
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
 * 
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
 * 
 * @param	string	$hookName
 * @param	mixed
 * @return void
 * @access public
 */
	function executeHook($hookName){
		
		$args = func_get_args();
		unset($args[0]);
		if($this->registerHooks && isset($this->registerHooks[$hookName])){
			foreach($this->registerHooks[$hookName] as $key => $pluginName) {
				call_user_func_array(array($this->pluginHooks[$pluginName], $hookName), $args);
			}
		}
		
	}
/**
 * startup
 * 
 * @param Controller $controller
 * @return void
 * @access public
 */
	function startup($controller) {
		
		$this->executeHook('startup',$controller);
		
	}
/**
 * beforeRender
 * 
 * @param Controller $controller
 * @return void
 * @access public
 */
	function beforeRender($controller) {
		
		$this->executeHook('beforeRender',$controller);
		
	}
/**
 * beforeRedirect
 * 
 * @param Controller $controller
 * @return void
 * @access public
 */
	function beforeRedirect($controller, $url, $status = null, $exit = true) {
		
		$this->executeHook('beforeRedirect', $controller, $url, $status, $exit);
		
	}
/**
 * shutdown
 * 
 * @param Controller $controller
 * @return void
 * @access public
 */
	function shutdown($controller) {
		
		$this->executeHook('shutdown', $controller);
		
	}
/**
 * afterPageAdd
 * 
 * @param Controller $controller
 * @return void
 * @access public
 */
	function afterPageAdd($controller) {
		
		$this->executeHook('afterPageAdd', $controller);
		
	}
/**
 * afterPageEdit
 * 
 * @param Controller $controller
 * @return void
 * @access public
 */
	function afterPageEdit($controller) {
		
		$this->executeHook('afterPageEdit', $controller);
		
	}
/**
 * call__ マジックメソッド
 *
 * @param string $method
 * @param array $params
 * @return mixed
 * @access protected
 */
	function call__($method, $params) {

		$args = func_get_args();
		$args = $args[1];
		$Object = $args[0];
		if(method_exists($Object, $method)){
			return call_user_func_array( array( $Object, $method ), $args );
		}

	}
}
