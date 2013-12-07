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
 * @package			Baser.Controller.Component
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
 * @package Baser.Controller.Component
 */
class BcAuthConfigureComponent extends Component {

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

		if (empty($this->controller->BcAuth)) {
			return false;
		}

		$controller = $this->controller;
		$auth = $controller->BcAuth;
		$requestedPrefix = '';

		if (isset($controller->params['prefix'])) {
			$requestedPrefix = $controller->params['prefix'];
		}

		$config = array_merge(array(
			'loginRedirect'  => '/' . $requestedPrefix,
			'logoutRedirect' => '',
			'username'       => 'name',
			'password'       => 'password',
			'serial'         => '',
			'userScope'      => null,
			'loginAction'    => ''
			), $config);
		extract($config);

		if (empty($userModel)) {
			$userModel = 'User';
		}

		// ログインアクション
		if (empty($loginAction)) {
			if ($requestedPrefix) {
				$loginAction = array('prefix' => $requestedPrefix, 'controller' => 'users', 'action' => 'login');
			} else {
				$loginAction = array('controller' => 'users', 'action' => 'login');
			}
		}
		$auth->loginAction = $loginAction;

		// ログアウト時のリダイレクト先
		if (!empty($logoutRedirect)) {
			$auth->logoutRedirect = $logoutRedirect;
		}

		// オートリダイレクトをOFF
		$auth->autoRedirect = false;

		// エラーメッセージ
		$auth->loginError = '入力されたログイン情報を確認できませんでした。もう一度入力してください。';

		// 権限が無いactionを実行した際のエラーメッセージ
		$auth->authError = '指定されたページを開くにはログインする必要があります。';
		$auth->authorize = 'Controller';

		// フォームの認証設定
		$auth->authenticate = array(
			'Form' => array(
				'userModel' => $userModel,
				'fields' => array(
					'username' => $username,
					'password' => $password
				),
				'serial' => $serial
			)
		);

		// 認証プレフィックスによるスコープ設定
		if (!empty($config['auth_prefix']) && !isset($scope)) {
			$auth->authenticate['Form']['scope'] = array('UserGroup.auth_prefix' => $config['auth_prefix']);
		} elseif (isset($scope)) {
			$auth->authenticate['Form']['scope'] = $scope;
		}

		// セッション識別
		// TODO 2013/05/27 ryuring
		// 静的プロパティの書き換えが外部よりできなかったのでメソッドを作って無理矢理対応
		// 現在のバージョン（3.0.0 beta）では、認証情報を複数持てる仕様となっていない
		// 上記仕様に対応させる為には、ここの処理変更だけでなく全体的な認証の仕組みを見直す必要あり
		$auth->setSessionKey('Auth.User');

		// 記録された過去のリダイレクト先が対象のプレフィックス以外の場合はリセット
		$redirect = $auth->Session->read('Auth.redirect');
		if ($redirect && $requestedPrefix && strpos($redirect, $requestedPrefix) === false) {
			$auth->Session->write('Auth.redirect', null);
		}

		// ログイン後にリダイレクトするURL
		$auth->loginRedirect = $loginRedirect;

		if (!$auth->user()) {

			// クッキーがある場合にはクッキーで認証
			if (!empty($controller->Cookie)) {
				$cookie = $controller->Cookie->read(BcAuthComponent::$sessionKey);
				if (!empty($cookie)) {
					if ($auth->login($cookie)) {
						return true;
					}
				}
			}

			// インストールモードの場合は無条件に認証なし
			if (Configure::read('debug') == -1) {
				$controller->Session->delete('Message.auth');
				$auth->allow();
			}
		}

		return true;
	}

}
