<?php
/* SVN FILE: $Id$ */
/**
 * リプレースプレフィックスコンポーネント
 *
 * 既に用意のあるプレフィックスアクションがある場合、
 * 違うプレフィックスでのアクセスを既にあるアクション、ビューに置き換える
 *
 * 【例】
 * /admin/users/login・・・admin_login が呼び出される
 * /mypage/users/login・・・admin_login が呼び出される
 *
 * リクエストしたプレフィックスに適応したアクションがある場合はそちらが優先される
 * リクエストしたプレフィックスに適応したビューが存在する場合はそちらが優先される
 *
 * 【注意事項】
 * ・baserCMS用のビューパスのサブディレクトリ化に依存している。
 * ・リクエストしたプレフィックスに適応したアクションが存在する場合は、ビューの置き換えは行われない。
 * ・Authと併用する場合は、コンポーネントの宣言で、Authより前に宣言しないと認証処理が動作しない。
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
class BcReplacePrefixComponent extends Object {
/**
 * プレフィックス置き換えを許可するアクション
 * プレフィックスなしの純粋なアクション名を指定する
 *
 * @var array
 * @access public
 */
	var $allowedPureActions = array();
/**
 * 置き換え後のプレフィックス
 *
 * @var string
 * @access public
 */
	var $replacedPrefix = 'admin';
/**
 * 対象コントローラーのメソッド
 *
 * @var array
 * @access	protected
 */
	var $_methods;
/**
 * Initializes
 *
 * @param Controller $controller
 * @return void
 * @access public
 */
	function initialize($controller) {
		
		$this->_methods = $controller->methods;
		
	}
/**
 * プレフィックスの置き換えを許可するアクションを設定する
 *
 * $this->Replace->allow('action', 'action',...);
 *
 * @param string $action
 * @param string $action
 * @param string ... etc.
 * @return void
 * @access public
 */
	function allow() {

		$args = func_get_args();
		if (isset($args[0]) && is_array($args[0])) {
			$args = $args[0];
		}
		$this->allowedPureActions = array_merge($this->allowedPureActions, $args);

	}
/**
 * startup
 *
 * @return	void
 * @access	public
 */
	function startup($controller) {

		if(in_array($controller->action, $this->_methods)) {
			return;
		}

		if(!isset($controller->params['prefix'])) {
			$requestedPrefix = '';
		} else {
			$requestedPrefix = $controller->params['prefix'];
		}

		$pureAction = preg_replace('/^'.$requestedPrefix.'_/', '', $controller->action);

		if(!in_array($pureAction, $this->allowedPureActions)) {
			return;
		}
		if(!in_array($this->replacedPrefix.'_'.$pureAction, $this->_methods)) {
			return;
		}

		$controller->action = $this->replacedPrefix.'_'.$pureAction;
		$controller->layoutPath = $this->replacedPrefix;	// Baserに依存
		$controller->subDir = $this->replacedPrefix;		// Baserに依存
		
		if($requestedPrefix != $this->replacedPrefix) {

			// viewファイルが存在すればリクエストされたプレフィックスを優先する
			$existsLoginView = false;
			$viewPaths = $this->getViewPaths($controller);
			$prefixPath = str_replace('_', DS, $requestedPrefix);
			$controllerName = Inflector::underscore($controller->name);
			foreach($viewPaths as $path) {
				if($prefixPath) {
					$file = $path.$controllerName.DS.$prefixPath.DS.$pureAction.$controller->ext;
				} else {
					$file = $path.$controllerName.DS.$pureAction.$controller->ext;
				}
				if(file_exists($file)) {
					$existsLoginView = true;
					break;
				}
			}

			if($existsLoginView) {
				$controller->subDir = $prefixPath;
				$controller->layoutPath = $prefixPath;
			}

		}

	}
/**
 * Return all possible paths to find view files in order
 *
 * @param string $plugin
 * @return array paths
 * @access private
 */
	function getViewPaths($controller) {

		$paths = Configure::read('viewPaths');

		if (!empty($controller->theme)) {
			$count = count($paths);
			for ($i = 0; $i < $count; $i++) {
				$themePaths[] = $paths[$i] . 'themed'. DS . $controller->theme . DS;
			}
			$paths = array_merge($themePaths, $paths);
		}
		return $paths;

	}

}
