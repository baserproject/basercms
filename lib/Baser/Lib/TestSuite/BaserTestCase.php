<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Lib.TestSuite
 * @since           baserCMS v 3.0.6
 * @license         https://basercms.net/license/index.html
 */

App::uses('BaserTestFixture', 'TestSuite/Fixture');

/**
 * Class BaserTestCase
 *
 * Baser Test Case
 *
 * @package Baser.Lib.TestSuite
 */
class BaserTestCase extends CakeTestCase
{

	/**
	 * {@inheritDoc}
	 */
	public function __construct($name = null, array $data = [], $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
		// ブラウザと、コンソールでCakeRequestの内容が違うので一旦トップページとして初期化する
		Configure::write('debug', 1);
		Configure::write('App.baseUrl', '');
		$this->_getRequest('/');
		// =====================================================================
		// 上記のBaserTestCase::_getRequest()実行時、 routes.php が呼び出され、
		// Pageモデル等が、テストモードでない状態でインスタンス化されてしまうので一旦、
		// ClassRegistry を初期化する
		// =====================================================================
		ClassRegistry::flush();
	}

	/**
	 * 指定されたURLに対応しRouterパース済のCakeRequestのインスタンスを返す
	 *
	 * @param string $url URL
	 * @return CakeRequest
	 */
	protected function _getRequest($url)
	{
		Router::$initialized = false;
		Router::reload();
		BcSite::flash();
		$request = new CakeRequest($url);

		// コンソールからのテストの場合、requestのパラメーターが想定外のものとなってしまうので調整
		if (isConsole()) {
			$baseUrl = Configure::read('App.baseUrl');
			if ($request->url === false) {
				$request->here = $baseUrl . '/';
			} elseif (preg_match('/^' . preg_quote($request->webroot, '/') . '/', $request->here)) {
				$request->here = $baseUrl . '/' . preg_replace('/^' . preg_quote($request->webroot, '/') . '/', '', $request->here);
			}
			if ($baseUrl) {
				if (preg_match('/^\//', $baseUrl)) {
					$request->base = $baseUrl;
				} else {
					$request->base = '/' . $baseUrl;
				}
				$request->webroot = $baseUrl;
			} else {
				$request->base = '';
				$request->webroot = '/';
			}
		}
		Router::setRequestInfo($request);
		$params = Router::parse($request->url);
		unset($params['?']);
		$request = Router::getRequest(true);
		$request->addParams($params);
		return $request;
	}

	/**
	 * 管理画面にログインする
	 *
	 * @param string $group
	 */
	protected function _loginAdmin($id = 1)
	{
		$key = Configure::read('BcAuthPrefix.admin.sessionKey');
		$User = ClassRegistry::init('User', 'Model');
		$user = $User->find('first', ['conditions' => ['User.id' => $id]]);
		$user['User']['UserGroup'] = $user['UserGroup'];
		$_SESSION['Auth'][$key] = $user['User'];
	}

	/**
	 * イベントを設定する
	 *
	 * @param $events
	 */
	public function attachEvent($events)
	{
		$EventManager = CakeEventManager::instance();
		$event = new BcEventListenerMock($events);
		$EventManager->attach($event);
		return $event;
	}

	/**
	 * イベントをリセットする
	 */
	public function resetEvent()
	{
		$EventManager = CakeEventManager::instance();
		$reflectionClass = new ReflectionClass(get_class($EventManager));
		$property = $reflectionClass->getProperty('_listeners');
		$property->setAccessible(true);
		$property->setValue($EventManager, []);
	}

}

/**
 * Class BcEventListenerMock
 */
class BcEventListenerMock extends CakeObject implements CakeEventListener
{
	public $events = [];

	/**
	 * BcEventListenerMock constructor.
	 * @param $events
	 */
	public function __construct($events)
	{
		$this->events = $events;
	}

	/**
	 * @return array
	 */
	public function implementedEvents()
	{
		return $this->events;
	}

}
