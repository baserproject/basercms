<?php
/* SVN FILE: $Id$ */
/**
 * プラグインフックヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * プラグインフックヘルパー
 * 
 * @package baser.views.helpers
 */
class BcPluginHookHelper extends AppHelper {
/**
 * プラグインフックオブジェクト
 * 
 * @var array
 * @access public
 */
	var $pluginHooks = array();
/**
 * 登録済フックメソッド

 * @var array
 * @access public
 */
	var $registerHooks = array();
/**
 * beforeRender
 * 
 * @return void
 * @access public
 */
	function beforeRender() {

		/* 未インストール・インストール中の場合はすぐリターン */
		if(!isInstalled ()) {
			return;
		}

		$view = ClassRegistry::getObject('View');
		$plugins = Configure::read('BcStatus.enablePlugins');
		// 現在のテーマのフックも登録する（テーマフック）
		// TODO プラグインではないので、プラグインフックというこの仕組の名称自体を検討する必要がある？
		$plugins[] = Configure::read('BcSite.theme');
		
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
 * 
 * @param string $hookName
 * @param string $pluginName
 * @return void
 * @access pubic
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
 * @param string $hookName
 * @param string $out
 * @return string $out
 * @access public
 */
	function executeHook($hookName, $return = null){

		$args = func_get_args();
		unset($args[0]);unset($args[1]);

		if($this->registerHooks && isset($this->registerHooks[$hookName])){
			foreach($args as $key => $arg) {
				if($arg === $return) {
					$j = $key;
					break;
				}
			}
			foreach($this->registerHooks[$hookName] as $key => $pluginName) {
				$return = call_user_func_array(array($this->pluginHooks[$pluginName], $hookName), $args);
				if(isset($j)) {
					$args[$j] = $return;
				}
			}
		}

		return $return;
		
	}
/**
 * afterRender
 * 
 * @return void
 * @access public
 */
	function afterRender() {
		
		$this->executeHook('afterRender');
		
	}
/**
 * beforeLayout
 * 
 * @return void
 * @access public
 */
	function beforeLayout() {
		
		$this->executeHook('beforeLayout');
		
	}
/**
 * afterLayout
 * 
 * @return void
 * @access public
 */
	function afterLayout() {
		
		$this->executeHook('afterLayout');
		
	}
/**
 * before Form::create
 * 
 * @param Form $form
 * @param string $model
 * @param array $options
 * @return array
 * @access public
 */
	function beforeFormCreate($form, $id, $model = null, $options = array()) {
		
		return $this->executeHook('beforeFormCreate', $options, $form, $id, $model, $options);
		
	}
/**
 * after Form::create
 * 
 * @param string $out
 * @return string
 * @access public
 */
	function afterFormCreate($form, $id, $out) {
		
		return $this->executeHook('afterFormCreate', $out, $form, $id, $out);
		
	}
/**
 * before Form::end
 * 
 * @param Form $form
 * @param string $model
 * @param array $options
 * @return array
 * @access public
 */
	function beforeFormEnd($form, $id, $options = array()) {
		
		return $this->executeHook('beforeFormEnd', $options, $form, $id, $options);
		
	}
/**
 * after Form::end
 * 
 * @param string $out
 * @return string
 * @access public
 */
	function afterFormEnd($form, $id, $out) {
		
		return $this->executeHook('afterFormEnd', $out, $form, $id, $out);
		
	}
/**
 * before Form::input
 * 
 * @param string $out
 * @return string
 * @access public
 */
	function beforeFormInput($form, $fieldName, $options = array()) {
		
		return $this->executeHook('beforeFormInput', $options, $form, $fieldName, $options);
		
	}
/**
 * after Form::input
 * 
 * @param string $out
 * @return string
 * @access public
 */
	function afterFormInput($form, $fieldName, $out) {
		
		return $this->executeHook('afterFormInput', $out, $form, $fieldName, $out);
		
	}
/**
 * beforeBaserGetLink
 * 
 * @param string $html
 * @param striing $title
 * @param string $url
 * @param array $htmlAttributes
 * @param boolean $confirmMessage
 * @param boolean $escapeTitle
 * @return string 
 */
	function beforeBaserGetLink($html, $title, $url = null, $htmlAttributes = array(), $confirmMessage = false, $escapeTitle = true) {
		
		return $this->executeHook('beforeBaserGetLink', $htmlAttributes, $html, $title, $url, $htmlAttributes, $confirmMessage, $escapeTitle);
		
	}
/**
 *
 * @param string $html
 * @param string $url
 * @param string $out
 * @return string 
 * @access public
 */
	function afterBaserGetLink($html, $url, $out) {
		
		return $this->executeHook('afterBaserGetLink', $out, $html, $url, $out);
		
	}
/**
 * baserHeader
 *
 * @param string $out
 * @return string
 * @access public
 */
	function baserHeader($baser, $out) {
		
		return $this->executeHook('baserHeader', $out, $out);
		
	}
/**
 * baserFooter
 *
 * @param string $out
 * @return string
 */
	function baserFooter($baser, $out) {
		
		return $this->executeHook('baserFooter', $out, $out);
		
	}
/**
 * Baser::beforeElement
 *
 * @param string $out
 * @return string
 */
	function beforeElement($baser, $name, $params = array(), $loadHelpers = false, $subDir = true) {
		
		return $this->executeHook('beforeElement', $params, $name, $params, $loadHelpers, $subDir);
		
	}
/**
 * Baser::beforeElement
 *
 * @param string $out
 * @return string
 */
	function afterElement($baser, $name, $out) {
		
		return $this->executeHook('afterElement', $out, $name, $out);
		
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