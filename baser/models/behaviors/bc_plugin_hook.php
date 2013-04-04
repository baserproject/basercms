<?php
/* SVN FILE: $Id$ */
/**
 * プラグインフックビヘイビア
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models.behavior
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
 * プラグインフックビヘイビア
 *
 * @package baser.models.behavior
 */
class BcPluginHookBehavior extends ModelBehavior {
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
 * ビヘイビア設定
 *
 * @var array
 * @access public
 * @see Model::$alias
 */
	var $settings = array();
/**
 * プラグインフックを登録する
 *
 * @param Model $model
 * @param string $hookName
 * @param string $pluginName
 * @return void
 * @access pubic
 */
	function registerHook($model, $hookName, $pluginName){

		if(!isset($this->registerHooks[$model->alias][$hookName])){
			$this->registerHooks[$model->alias][$hookName] = array();
		}
		$this->registerHooks[$model->alias][$hookName][] = $pluginName;

	}
/**
 * プラグインフックを実行する
 *
 * @param Model $model
 * @param string $hookName
 * @param mixed $return
 * @return mixed
 * @access public
 */
	function executeHook($model, $hookName, $return = null){

		$args = func_get_args();
		unset($args[1]);unset($args[2]);

		if($this->registerHooks && isset($this->registerHooks[$model->alias][$hookName])){
			foreach($args as $key => $arg) {
				if($arg === $return) {
					$j = $key;
					break;
				}
			}
			foreach($this->registerHooks[$model->alias][$hookName] as $pluginName) {
				$return = call_user_func_array(array($this->pluginHooks[$pluginName], $hookName), $args);
				if(isset($j)) {
					$args[$j] = $return;
				}
			}
		}
		return $return;

	}
/**
 * Setup
 *
 * @param object $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @access public
 */
	function setup($model, $config = array()) {

		/* 未インストール・インストール中の場合はすぐリターン */
		if(!isInstalled ()) {
			return;
		}

		$plugins = Configure::read('BcStatus.enablePlugins');
		
		if(!$plugins) {
			return;
		}
		
		/* プラグインフックコンポーネントが実際に存在するかチェックしてふるいにかける */
		$pluginHooks = array();
		if($plugins) {
			foreach($plugins as $plugin) {
				$pluginName = Inflector::camelize($plugin);
				if(App::import('Behavior', $pluginName.'.'.$pluginName.'Hook')) {
					$pluginHooks[] = $pluginName;
				}
			}
		}
		
		/* プラグインフックを初期化 */
		foreach($pluginHooks as $pluginName) {

			$className = $pluginName.'HookBehavior';
			$this->pluginHooks[$pluginName] =& new $className();

			// 各プラグインの関数をフックに登録する
			if(isset($this->pluginHooks[$pluginName]->registerHooks)){
				foreach ($this->pluginHooks[$pluginName]->registerHooks as $key => $hookNames){
					if($model->alias === $key || is_numeric($key)) {
						if(!is_array($hookNames)) {
							$hookNames = array($hookNames);
						}
						foreach ($hookNames as $hookName) {
							$this->registerHook($model, $hookName, $pluginName);
						}
					}
				}
			}

		}

		/* setup のフックを実行 */
		$this->executeHook($model, 'setup', null, $config);

	}
/**
 * beforeFind
 *
 * @param object $model
 * @param array $queryData
 * @return boolean
 * @access public
 */
	function beforeFind($model, $query) {
		return $this->executeHook($model, 'beforeFind', $query, $query);
	}
/**
 * afterFind
 *
 * @param object $model
 * @param mixed $results
 * @param boolean $primary
 * @return mixed
 * @access public
 */
	function afterFind($model, $results, $primary) {
		
		return $this->executeHook($model, 'afterFind', $results, $results, $primary);
		
	}
/**
 * beforeValidate
 *
 * @param object $model
 * @return boolean
 * @access public
 */
	function beforeValidate($model) {
		
		return $this->executeHook($model, 'beforeValidate', true);
		
	}
/**
 * beforeSave
 *
 * @param object $model
 * @return boolean
 * @access public
 */
	function beforeSave($model) {
		
		return $this->executeHook($model, 'beforeSave', true);
		
	}
/**
 * afterSave
 *
 * @param object $model
 * @param boolean $created
 * @access public
 */
	function afterSave($model, $created) {
		
		$this->executeHook($model, 'afterSave', null, $created);
		
	}
/**
 * beforeDelete
 *
 * @param object $model
 * @param boolean $cascade
 * @return boolean
 * @access public
 */
	function beforeDelete($model, $cascade = true) {
		
		return $this->executeHook($model, 'beforeDelete', true, $cascade);
		
	}
/**
 * afterDelete
 *
 * @param object $model
 * @access public
 */
	function afterDelete($model) {
		
		$this->executeHook($model, 'afterDelete');
		
	}
/**
 * onError
 *
 * @param object $model
 * @param string $error
 * @access public
 */
	function onError($model, $error) {
		
		$this->executeHook($model, 'onError', null, $error);
		
	}
/**
 * Magic method handler.
 *
 * @param string $method Method name
 * @param array $params Parameters to send to method
 * @return mixed Return value from method
 * @access private
 */
	function __call($method, $params) {
		if (!method_exists($this, 'call__')) {
			trigger_error(sprintf(__('Magic method handler call__ not defined in %s', true), get_class($this)), E_USER_ERROR);
		}
		return $this->call__($method, $params);
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
