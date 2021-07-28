<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller.Component
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('Component', 'Controller');

/**
 * Class BcAuthConfigureComponent
 *
 * 認証設定コンポーネント
 *
 * @package Baser.Controller.Component
 */
class BcAuthConfigureComponent extends Component
{

	/**
	 * コントローラー
	 *
	 * @var Controller
	 */
	public $_Controller = null;

	/**
	 * initialize
	 *
	 * @param object $Controller
	 * @return void
	 */
	public function initialize(Controller $Controller)
	{
		$this->_Controller = $Controller;
	}

	/**
	 * 認証設定
	 *
	 * @param string $config
	 * @return boolean
	 */
	public function setting($config)
	{
		if (empty($this->_Controller->BcAuth) || !$config) {
			return false;
		}

		$Controller = $this->_Controller;
		$BcAuth = $Controller->BcAuth;
		$requestedPrefix = '';

		if (isset($Controller->params['prefix'])) {
			$requestedPrefix = $Controller->params['prefix'];
		}

		$config = array_merge([
			'loginRedirect' => '/' . $requestedPrefix,
			'logoutRedirect' => '',
			'username' => 'name',
			'password' => 'password',
			'serial' => '',
			'loginAction' => '',
			'userModel' => 'User',
			'auth_prefix' => '',
			'userScope' => '',
			'sessionKey' => Configure::read('BcAuthPrefix.admin.sessionKey')
		], $config);
		if (empty($config['type'])) {
			$config['type'] = 'Form';
		}

		// ログインアクション
		if (!$config['loginAction']) {
			if ($requestedPrefix) {
				$config['loginAction'] = ['prefix' => $requestedPrefix, 'controller' => 'users', 'action' => 'login'];
			} else {
				$config['loginAction'] = ['controller' => 'users', 'action' => 'login'];
			}
		}
		$BcAuth->loginAction = $config['loginAction'];

		// ログアウト時のリダイレクト先
		if ($config['logoutRedirect']) {
			$BcAuth->logoutRedirect = $config['logoutRedirect'];
		}

		// オートリダイレクトをOFF
		$BcAuth->autoRedirect = false;

		// エラーメッセージ
		$BcAuth->loginError = __d('baser', '入力されたログイン情報を確認できませんでした。もう一度入力してください。');

		// 権限が無いactionを実行した際のエラーメッセージ
		$BcAuth->authError = false;
		$BcAuth->authorize = 'Controller';
		// =====================================================================
		// 権限判定失敗時のリダイレクト先
		// 復数の認証プレフィックスに対してログインできる仕様で、片方のログインが完了している場合
		// もう片方にアクセスした場合、権限判定に失敗し、loginRedirect にリダイレクトする仕様となっているが、
		// リダイレクトせずに強制的にエラー表示とする。
		// 理由は、loginRedirect にリダイレクトした場合、再度、判定に失敗し、
		// 無限リダイレクトが発生してしまう為。
		// =====================================================================
		$BcAuth->unauthorizedRedirect = $BcAuth->loginAction;

		// フォームの認証設定
		$BcAuth->authenticate = [
			$config['type'] => [
				'userModel' => $config['userModel'],
				'fields' => [
					'username' => $config['username'],
					'password' => $config['password']
				],
				'serial' => $config['serial']
			]
		];

		// 認証プレフィックスによるスコープ設定
		$UserModel = ClassRegistry::init($config['userModel']);
		if (isset($UserModel->belongsTo['UserGroup']) && $config['auth_prefix'] && !$config['userScope']) {
			$BcAuth->authenticate['Form']['scope'] = ['UserGroup.auth_prefix LIKE' => '%' . $config['auth_prefix'] . '%'];
		} elseif ($config['userScope']) {
			$BcAuth->authenticate['Form']['scope'] = $config['userScope'];
		}
		// 認証プレフィックスによるパスワードハッシャー設定
		if (isset($config['passwordHasher'])) {
			$BcAuth->authenticate['Form']['passwordHasher'] = $config['passwordHasher'];
		}

		// セッション識別
		// TODO 2013/05/27 ryuring
		// 静的プロパティの書き換えが外部よりできなかったのでメソッドを作って無理矢理対応
		// 現在のバージョン（3.0.0 beta）では、認証情報を複数持てる仕様となっていない
		// 上記仕様に対応させる為には、ここの処理変更だけでなく全体的な認証の仕組みを見直す必要あり
		$BcAuth->setSessionKey('Auth.' . $config['sessionKey']);

		// 記録された過去のリダイレクト先が対象のプレフィックス以外の場合はリセット
		// 2016/06/09 ryuring
		// 必要性がないようなので一旦コメントアウト
//		$redirect = $BcAuth->Session->read('Auth.redirect');
//		if ($redirect && $requestedPrefix && strpos($redirect, $requestedPrefix) === false) {
//			$BcAuth->Session->write('Auth.redirect', null);
//		}

		// ログイン後にリダイレクトするURL
		$BcAuth->loginRedirect = $config['loginRedirect'];

		if (!$BcAuth->user()) {

			// クッキーがある場合にはクッキーで認証
			if (!empty($Controller->Cookie) && $Controller->request->is('requestview') !== false) {
				$cookieKey = Inflector::camelize(str_replace('.', '', BcAuthComponent::$sessionKey));
				$cookie = $Controller->Cookie->read($cookieKey);

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
					if (is_array($cookie)) {
						list($plugin, $userModel) = pluginSplit($config['userModel']);
						if (!empty($Controller->request->data[$userModel])) {
							$requestData = $Controller->request->data[$userModel];
						}
						$Controller->request->data[$userModel] = $cookie;
						$loginResult = $BcAuth->login();
						unset($Controller->request->data[$userModel]);
						if (!empty($requestData)) {
							$Controller->request->data[$userModel] = $requestData;
						}
						if ($loginResult) {
							return true;
						}
					}
					$Controller->Cookie->write($cookieKey, null);
				}
			}

			// インストールモードの場合は無条件に認証なし
			if (Configure::read('debug') == -1) {
				$Controller->Session->delete('Message.auth');
				$BcAuth->allow();
			}
		}

		return true;
	}

}
