<?php

/**
 * 認証設定コンポーネント
 * (注）BaserのDB設計に依存している
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller.Component
 * @since			baserCMS v 0.1.0
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
			'loginRedirect'		=> '/' . $requestedPrefix,
			'logoutRedirect'	=> '',
			'username'			=> 'name',
			'password'			=> 'password',
			'serial'			=> '',
			'loginAction'		=> ''
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
		if (!empty($config['auth_prefix']) && !isset($userScope)) {
			$auth->authenticate['Form']['scope'] = array('UserGroup.auth_prefix LIKE' => '%' . $config['auth_prefix'] . '%');
		} elseif (isset($userScope)) {
			$auth->authenticate['Form']['scope'] = $userScope;
		}

		if(empty($sessionKey)) {
			$sessionKey = Configure::read('BcAuthPrefix.admin.sessionKey');
		}

		// セッション識別
		// TODO 2013/05/27 ryuring
		// 静的プロパティの書き換えが外部よりできなかったのでメソッドを作って無理矢理対応
		// 現在のバージョン（3.0.0 beta）では、認証情報を複数持てる仕様となっていない
		// 上記仕様に対応させる為には、ここの処理変更だけでなく全体的な認証の仕組みを見直す必要あり
		$auth->setSessionKey('Auth.' . $sessionKey);

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
				$cookie = $controller->Cookie->read(Inflector::camelize(str_replace('.', '', BcAuthComponent::$sessionKey)));
				
				// ===================================================================================
				// 2014/06/19 ryuring
				// PHPの仕様として、ある条件にてクッキーを削除した際、クッキーの値に deleted が設定されてしまうので、
				// deleted が設定されている場合は、クッキーを無視する仕様に変更した
				// 《参考情報》
				// http://siguniang.wordpress.com/2009/08/19/phpcookieを削除すると値をdeletedに設定/
				// 上記参考情報には、「クライアントPCの時刻を1年以上昔に設定」とあるが、そうしない場合も再現できた
				// その原因までは追っていない
				// ===================================================================================
				
				if (!empty($cookie) && $cookie != 'deleted') {
					$controller->request->data[$userModel] = $cookie;
					if ($auth->login()) {
						return true;
					} else {
						$controller->request->data[$userModel] = null;
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
