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
/**
 * initialize
 *
 * @param   object  $controller
 * @return 	void
 * @access	public
 */
    function initialize(&$controller) {

        $this->setting($controller);
        
    }
/**
 * 認証設定
 *
 * @param   object  $controller
 * @return 	void
 * @access	public
 */
    function setting(&$controller) {

		$cookie = $controller->Cookie->read($controller->Auth->sessionKey);

		/* 認証設定 */
		if(!empty($controller->params['admin'])){

			$redirect = $controller->Auth->Session->read('Auth.redirect');
			// 記録された過去のリダイレクト先が管理者ページ以外の場合はリセット
			if(strpos($controller->Auth->Session->read('Auth.redirect'),'admin')===false){
				$controller->Auth->Session->write('Auth.redirect',null);
			}
			// ログイン条件
			$controller->Auth->userScope = array('User.authority_group'=>1);
			// ログイン後にリダイレクトするURL
			$controller->Auth->loginRedirect = '/admin/dashboard/index';
			// ログインアクション
			$controller->Auth->loginAction = '/admin/users/login';
			// セッション識別
			$controller->Auth->sessionKey = 'Auth.AdminUser';

			if(isset($cookie['Auth']['AdminUser']))
				$authCookie = $cookie['Auth']['AdminUser'];

		}elseif(isset($controller->params['prefix']) && $controller->params['prefix'] == 'member'){

			// 記録された過去のリダイレクト先がメンバーマイページ以外の場合はリセット
			if(strpos($controller->Auth->Session->read('Auth.redirect'),'member')===false){
				$controller->Auth->Session->write('Auth.redirect',null);
			}
			// ログイン条件
			$controller->Auth->userScope = array('User.authority_group'=>2);
			// ログイン後にリダイレクトするURL
			$controller->Auth->loginRedirect = '/member/dashboard/index';
			// ログインアクション
			$controller->Auth->loginAction = '/member/users/login';
			// セッション識別
			$controller->Auth->sessionKey = 'Auth.MypageUser';

			if(isset($cookie['Auth']['MypageUser']))
				$authCookie = $cookie['Auth']['MypageUser'];

		}

		// オートリダイレクトをOFF
		$controller->Auth->autoRedirect = false;
		// エラーメッセージ
		$controller->Auth->loginError = '入力されたログイン情報を確認できませんでした。もう一度入力して下さい。';
		// 権限が無いactionを実行した際のエラーメッセージ
		$controller->Auth->authError = '該当のページを開くにはログインする必要があります。';
		//ユーザIDとパスワードのフィールドを指定
		$controller->Auth->fields = array('username' => 'name', 'password' => 'password');
		// ユーザIDとパスワードがあるmodelを指定('User'がデフォルト)
		//$controller->Auth->userModel = 'User';

		// クッキーがある場合には認証なし
        if ((!empty($authCookie) && $controller->Auth->login($authCookie))||Configure::read('debug')>0) {
            $controller->Session->del('Message.auth');
			$controller->Auth->allow();
        }

    }
    
}

?>