<?php
/* SVN FILE: $Id$ */
/**
 * 認証設定コンポーネント
 * (注）BaserのDB設計に依存している
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
 * @package			baser.controllers.components
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * 認証設定コンポーネント
 *
 * @package			baser.controllers.components
 */
class  AuthConfigureComponent extends Object {

	var $controller = null;
/**
 * initialize
 *
 * @param   object  $controller
 * @return 	void
 * @access	public
 */
    function initialize(&$controller) {
        $this->controller = $controller;
    }
/**
 * 認証設定
 *
 * @param   string  prefix
 * @return 	boolean
 * @access	public
 */
    function setting($prefix) {

		if(!$prefix){
			return false;
		}

		/* 認証設定 */

		$redirect = $this->controller->Auth->Session->read('Auth.redirect');
		// 記録された過去のリダイレクト先が管理者ページ以外の場合はリセット
		if(strpos($redirect, $prefix)===false){
			$this->controller->Auth->Session->write('Auth.redirect',null);
		}

		// ログイン後にリダイレクトするURL
		$this->controller->Auth->loginRedirect = '/'.$prefix;
		// ログインアクション
		$this->controller->Auth->loginAction = '/'.$prefix.'/users/login';
		// セッション識別
		$this->controller->Auth->sessionKey = 'Auth.User';

		$cookie = $this->controller->Cookie->read($this->controller->Auth->sessionKey);

		if(isset($cookie['Auth']['User']))
			$authCookie = $cookie['Auth']['User'];
		
		// オートリダイレクトをOFF
		$this->controller->Auth->autoRedirect = false;
		// エラーメッセージ
		$this->controller->Auth->loginError = '入力されたログイン情報を確認できませんでした。もう一度入力して下さい。';
		// 権限が無いactionを実行した際のエラーメッセージ
		$this->controller->Auth->authError = '該当のページを開くにはログインする必要があります。';
		//ユーザIDとパスワードのフィールドを指定
		$this->controller->Auth->fields = array('username' => 'name', 'password' => 'password');
		// ユーザIDとパスワードがあるmodelを指定('User'がデフォルト)
		//$this->controller->Auth->userModel = 'User';

		// クッキーがある場合には認証なし
        if ((!empty($authCookie) && $this->controller->Auth->login($authCookie))||Configure::read('debug')>0) {
            $this->controller->Session->del('Message.auth');
			$this->controller->Auth->allow();
        }

		return true;
		
    }

}
?>