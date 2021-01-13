<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Controller.Component
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcAuthConfigureComponent', 'Controller/Component');
App::uses('Controller', 'Controller');

/**
 * Class BcAuthConfigureTestController
 *
 * @package Baser.Test.Case.Controller.Component
 */
class BcAuthConfigureTestController extends Controller
{

	public $components = ['BcAuthConfigure', 'BcAuth', 'Cookie', 'Session'];

	public $authenticate = ['Form'];

}

/**
 * Class BcAuthConfigureComponentTest
 *
 * @package Baser.Test.Case.Controller.Component
 */
class BcAuthConfigureComponentTest extends BaserTestCase
{
	public $fixtures = [
		'baser.Default.User',
		'baser.Default.UserGroup',
	];

	public $components = ['BcAuthConfigure'];

	public function setUp()
	{
		parent::setUp();

		// コンポーネントと偽のコントローラをセットアップする
		$request = new CakeRequest();
		$response = $this->getMock('CakeResponse');
		$this->Controller = new BcAuthConfigureTestController($request, $response);

		$collection = new ComponentCollection();
		$collection->init($this->Controller);

		$this->BcAuthConfigure = new BcAuthConfigureComponent($collection);
		$this->BcAuthConfigure->request = $request;
		$this->BcAuthConfigure->response = $response;

		$this->Controller->Components->init($this->Controller);

		Router::reload();
		Router::connect('/:controller/:action/*');

	}

	public function tearDown()
	{
		session_unset();
		parent::tearDown();
		unset($this->Controller);
		unset($this->BcAuthConfigure);

	}

	/**
	 * 認証設定
	 * 初期化チェック
	 *
	 * @param string $config
	 * @return boolean
	 */
	public function testSettingCheckInitialize()
	{
		// 異常系
		$result = $this->Controller->BcAuthConfigure->setting([1]);
		$this->assertFalse($result, '初期化がされていない場合にtrueが返ってきます');

		// 正常系
		$this->Controller->BcAuthConfigure->initialize($this->Controller);
		$result = $this->Controller->BcAuthConfigure->setting([1]);
		$this->assertTrue($result, '初期化がされている場合にfalseが返ってきます');

	}

	/**
	 * 認証設定
	 * 代入チェック
	 *
	 * @dataProvider settingCheckValueDataProvider
	 */
	public function testSettingCheckValue($loginAction, $requestedPrefix, $userScope, $auth_prefix)
	{

		// 初期化
		$this->Controller->BcAuthConfigure->initialize($this->Controller);

		$config = [
			'loginRedirect' => 'login',
			'logoutRedirect' => 'logout',
			'username' => 'basercms',
			'password' => 'basercms',
			'serial' => 'serial',
			'loginAction' => $loginAction,
			'userScope' => $userScope,
			'auth_prefix' => $auth_prefix
		];

		$this->Controller->params['prefix'] = $requestedPrefix;

		// 認証設定を設定
		$this->Controller->BcAuthConfigure->setting($config);

		// 結果取得
		$result = [
			'loginAction' => $this->Controller->BcAuth->loginAction,
			'logoutRedirect' => $this->Controller->BcAuth->logoutRedirect,
			'unauthorizedRedirect' => $this->Controller->BcAuth->unauthorizedRedirect,
			'authenticate' => $this->Controller->BcAuth->authenticate,
			'sessionKey' => $this->Controller->BcAuth->sessionKey,
			'redirect' => $this->Controller->BcAuth->Session->read('Auth.redirect')
		];

		// 期待値の代入
		$expected = [
			'logoutRedirect' => 'logout',
			'authenticate' => [
				'Form' => [
					'userModel' => 'User',
					'fields' => ['username' => 'basercms', 'password' => 'basercms'],
					'serial' => 'serial',
				],
			],
			'sessionKey' => null,
			'redirect' => null
		];

		// loginAction
		if (empty($loginAction)) {
			if ($requestedPrefix) {
				$expected['loginAction'] = ['prefix' => $requestedPrefix, 'controller' => 'users', 'action' => 'login'];
			} else {
				$expected['loginAction'] = ['controller' => 'users', 'action' => 'login'];
			}
		} else {
			$expected['loginAction'] = $loginAction;
		}
		$expected['unauthorizedRedirect'] = $expected['loginAction'];

		// userScope
		if (!empty($userScope)) {
			$expected['authenticate']['Form']['scope'] = $userScope;
		} else if (!empty($auth_prefix)) {
			$expected['authenticate']['Form']['scope'] = ['UserGroup.auth_prefix LIKE' => '%' . $auth_prefix . '%'];
		}

		// 判定
		$this->assertEquals($expected, $result, '認証設定が正しくありません');

	}

	public function settingCheckValueDataProvider()
	{
		return [
			['login', 'pre', 'userScope', 'auth_prefix'],
			[null, 'pre', 'userScope', 'auth_prefix'],
			[null, null, 'userScope', 'auth_prefix'],
			['login', 'pre', null, 'auth_prefix'],
			['login', 'pre', null, null],
			['login', '/admin', 'userScope', 'auth_prefix'],
		];
	}

	/**
	 * 認証設定
	 * ログインチェック
	 *
	 * @dataProvider settingCheckLoginModeDataProvider
	 */
	public function testSettingCheckLogin($password)
	{

		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		// 初期化
		$this->Controller->BcAuthConfigure->initialize($this->Controller);

		$this->Controller->Cookie->write('AuthUser', [1]);
		$this->Controller->BcAuth->request = new CakeRequest();
		$this->Controller->BcAuth->response = new CakeResponse();

		// ログイン情報
		$this->Controller->BcAuth->request->data('User.name', 'basertest');
		$this->Controller->BcAuth->request->data('User.password', $password);

		// 認証設定を設定
		$this->Controller->BcAuthConfigure->setting([1]);

		// 結果を取得
		$user = $this->Controller->Session->read('Auth.User');
		$data = $this->Controller->request->data['User'];

		// 判定
		if ($password == 'basercms') {
			$this->assertContains('basertest', $user, 'ログインでの認証が正しく行えません');
			$this->assertEquals([1], $data, 'ログインでの認証が正しく行えません');

		} else {
			$this->assertNull($user, 'ログインでの認証が正しく行えません');
			$this->assertNull($data, 'ログインでの認証が正しく行えません');

		}

	}

	public function settingCheckLoginModeDataProvider()
	{
		return [
			['basercms'],
			['hoge'],
		];
	}

	/**
	 * 認証設定
	 * debugのモードチェック
	 *
	 * @dataProvider settingCheckDebugModeDataProvider
	 */
	public function testSettingCheckDebugMode($mode)
	{

		// 初期化
		$this->Controller->Session->write('Message.auth', 'Message');
		$this->Controller->methods = ['method1', 'method2'];

		$this->Controller->BcAuthConfigure->initialize($this->Controller);
		$this->Controller->BcAuth->initialize($this->Controller);

		Configure::write('debug', $mode);

		// 認証設定を設定
		$this->Controller->BcAuthConfigure->setting([1]);

		// 結果を取得
		$result = [
			'message' => $this->Controller->Session->read('Message.auth'),
			'allowedActions' => $this->Controller->BcAuth->allowedActions,
		];

		// 期待値の代入
		if ($mode == -1) {
			$expected = [
				'message' => null,
				'allowedActions' => ['method1', 'method2'],
			];

		} else {
			$expected = [
				'message' => 'Message',
				'allowedActions' => [],
			];
		}

		$this->assertEquals($expected, $result, 'インストールモードの場合のみに無条件に認証なしになりません');

	}

	public function settingCheckDebugModeDataProvider()
	{
		return [
			[0],
			[-1],
		];
	}


}

