<?php
/* SVN FILE: $Id$ */
/**
 * 認証設定コンポーネント
 * (注）BaserのDB設計に依存している
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
App::uses('Component', 'Controller');
/**
 * 認証設定コンポーネント
 *
 * @package baser.controllers.components
 */

class  BcAuthConfigureComponent extends Component {
/**
 * コントローラー
 * 
 * @var Controller
 * @access	public
 */
	public $controller = null;
/**
 * initialize
 *
 * @param object $controller
 * @return void
 * @access public
 */
	public function initialize(Controller $controller) {
		
		$this->controller = $controller;
		
	}
/**
 * 認証設定
 *
 * @param string $config
 * @return boolean
 * @access public
 */
	public function setting($config) {

		if(empty($this->controller->BcAuth)) {
			return false;
		}

		$controller = $this->controller;
		$auth = $controller->BcAuth;
		$requestedPrefix = '';
		
		if(isset($controller->params['prefix'])) {
			$requestedPrefix = $controller->params['prefix'];
		}
		
		$_config = array(
			'loginRedirect' => '/'.$requestedPrefix,
			'username'		=> 'name',
			'password'		=> 'password',
			'serial'		=> '',
			'userScope'		=> null,
			'loginAction'	=> ''
		);
		$config = array_merge($_config, $config);
		extract($config);

		if(empty($userModel)) {
			$userModel = 'User';
		}
		if(empty($loginAction)) {
			$loginAction = '/'.$requestedPrefix.'/users/login';
		}
		// オートリダイレクトをOFF
		$auth->autoRedirect = false;
		// エラーメッセージ
		$auth->loginError = '入力されたログイン情報を確認できませんでした。もう一度入力してください。';
		// 権限が無いactionを実行した際のエラーメッセージ
		$auth->authError = '指定されたページを開くにはログインする必要があります。';
		$auth->authorize = 'Controller';
		
		// ユーザIDとパスワードがあるmodelを指定('User'がデフォルト)
		//ユーザIDとパスワードのフィールドを指定
		$auth->authenticate = array(
			'Form'	=> array(
				'userModel'	=> $userModel,
				'fields'	=> array(
					'username'	=> $username,
					'password'	=> $password
					),
				'serial'	=> $serial
			)
		);

		// 認証プレフィックス
		if(!empty($config['auth_prefix']) && !isset($userScope)) {
			$auth->userScope = array('UserGroup.auth_prefix' => $config['auth_prefix']);
		} elseif(isset($userScope)) {
			$auth->userScope = $userScope;
		}
		
		// セッション識別
		// TODO basercamp 2013/05/27 ryuring
		// 静的プロパティの書き換えが外部よりできなかったのでメソッドを作って無理矢理対応
		$auth->setSessionKey('Auth.User');
		
		// ログインアクション
		$auth->loginAction = $loginAction;

		// 記録された過去のリダイレクト先が対象のプレフィックス以外の場合はリセット
		$redirect = $auth->Session->read('Auth.redirect');
		if($redirect && $requestedPrefix && strpos($redirect, $requestedPrefix)===false) {
			$auth->Session->write('Auth.redirect',null);
		}

		// ログイン後にリダイレクトするURL
		$auth->loginRedirect = $loginRedirect;

		if(!$auth->user()) {
			// クッキーがある場合にはクッキーで認証
			$cookie = $controller->Cookie->read(BcAuthComponent::$sessionKey);
			if(!empty($cookie)) {
				$auth->login($cookie);
				$this->setSessionAuthPrefix();
				return true;
			}
			// インストールモードの場合は無条件に認証なし
			if(Configure::read('debug')==-1) {
				$controller->Session->delete('Message.auth');
				$auth->allow();
			}
		}
		
		return true;

	}
/**
 * ログイン時にセッションにauthPrefixを保存する 
 */
	function setSessionAuthPrefix() {
		
		$authPrefix = $this->controller->Session->read(BcAuthComponent::$sessionKey . '.authPrefix');
		if (!$authPrefix) {
			if(empty($this->controller->params['prefix'])) {
				$authPrefix = 'front';
			} else {
				$authPrefix = $this->controller->params['prefix'];
			}
			$this->controller->Session->write(BcAuthComponent::$sessionKey . '.authPrefix', $authPrefix);
		}
		
	}

}
