<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Test.Case.Controller.Component.Auth
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AuthComponent', 'Controller/Component');
App::uses('FormAuthenticate', 'Controller/Component/Auth');
App::uses('AppModel', 'Model');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');

require_once CAKE . 'Test' . DS . 'Case' . DS . 'Model' . DS . 'models.php';

/**
 * Test case for FormAuthentication
 *
 * @package       Cake.Test.Case.Controller.Component.Auth
 */
class FormAuthenticateTest extends CakeTestCase {

/**
 * Fixtrues
 *
 * @var array
 */
	public $fixtures = ['core.user', 'core.auth_user'];

/**
 * setup
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Collection = $this->getMock('ComponentCollection');
		$this->auth = new FormAuthenticate($this->Collection, [
			'fields' => ['username' => 'user', 'password' => 'password'],
			'userModel' => 'User'
		]);
		$password = Security::hash('password', null, true);
		$User = ClassRegistry::init('User');
		$User->updateAll(['password' => $User->getDataSource()->value($password)]);
		$this->response = $this->getMock('CakeResponse');
	}

/**
 * test applying settings in the constructor
 *
 * @return void
 */
	public function testConstructor() {
		$object = new FormAuthenticate($this->Collection, [
			'userModel' => 'AuthUser',
			'fields' => ['username' => 'user', 'password' => 'password']
		]);
		$this->assertEquals('AuthUser', $object->settings['userModel']);
		$this->assertEquals(['username' => 'user', 'password' => 'password'], $object->settings['fields']);
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateNoData() {
		$request = new CakeRequest('posts/index', false);
		$request->data = [];
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateNoUsername() {
		$request = new CakeRequest('posts/index', false);
		$request->data = ['User' => ['password' => 'foobar']];
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateNoPassword() {
		$request = new CakeRequest('posts/index', false);
		$request->data = ['User' => ['user' => 'mariano']];
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test authenticate password is false method
 *
 * @return void
 */
	public function testAuthenticatePasswordIsFalse() {
		$request = new CakeRequest('posts/index', false);
		$request->data = [
			'User' => [
				'user' => 'mariano',
				'password' => null
		]];
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * Test for password as empty string with _checkFields() call skipped
 * Refs https://github.com/cakephp/cakephp/pull/2441
 *
 * @return void
 */
	public function testAuthenticatePasswordIsEmptyString() {
		$request = new CakeRequest('posts/index', false);
		$request->data = [
			'User' => [
				'user' => 'mariano',
				'password' => ''
		]];

		$this->auth = $this->getMock(
			'FormAuthenticate',
			['_checkFields'],
			[
				$this->Collection,
				[
					'fields' => ['username' => 'user', 'password' => 'password'],
					'userModel' => 'User'
				]
			]
		);

		// Simulate that check for ensuring password is not empty is missing.
		$this->auth->expects($this->once())
			->method('_checkFields')
			->will($this->returnValue(true));

		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test authenticate field is not string
 *
 * @return void
 */
	public function testAuthenticateFieldsAreNotString() {
		$request = new CakeRequest('posts/index', false);
		$request->data = [
			'User' => [
				'user' => ['mariano', 'phpnut'],
				'password' => 'my password'
		]];
		$this->assertFalse($this->auth->authenticate($request, $this->response));

		$request->data = [
			'User' => [
				'user' => [],
				'password' => 'my password'
		]];
		$this->assertFalse($this->auth->authenticate($request, $this->response));

		$request->data = [
			'User' => [
				'user' => 'mariano',
				'password' => ['password1', 'password2']
		]];
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateInjection() {
		$request = new CakeRequest('posts/index', false);
		$request->data = [
			'User' => [
				'user' => '> 1',
				'password' => "' OR 1 = 1"
		]];
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test authenticate success
 *
 * @return void
 */
	public function testAuthenticateSuccess() {
		$request = new CakeRequest('posts/index', false);
		$request->data = ['User' => [
			'user' => 'mariano',
			'password' => 'password'
		]];
		$result = $this->auth->authenticate($request, $this->response);
		$expected = [
			'id' => 1,
			'user' => 'mariano',
			'created' => '2007-03-17 01:16:23',
			'updated' => '2007-03-17 01:18:31'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test scope failure.
 *
 * @return void
 */
	public function testAuthenticateScopeFail() {
		$this->auth->settings['scope'] = ['user' => 'nate'];
		$request = new CakeRequest('posts/index', false);
		$request->data = ['User' => [
			'user' => 'mariano',
			'password' => 'password'
		]];

		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * Test that username of 0 works.
 *
 * @return void
 */
	public function testAuthenticateUsernameZero() {
		$User = ClassRegistry::init('User');
		$User->updateAll(['user' => $User->getDataSource()->value('0')], ['user' => 'mariano']);

		$request = new CakeRequest('posts/index', false);
		$request->data = ['User' => [
			'user' => '0',
			'password' => 'password'
		]];

		$expected = [
			'id' => 1,
			'user' => '0',
			'created' => '2007-03-17 01:16:23',
			'updated' => '2007-03-17 01:18:31'
		];
		$this->assertEquals($expected, $this->auth->authenticate($request, $this->response));
	}

/**
 * test a model in a plugin.
 *
 * @return void
 */
	public function testPluginModel() {
		Cache::delete('object_map', '_cake_core_');
		App::build([
			'Plugin' => [CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS],
		], App::RESET);
		CakePlugin::load('TestPlugin');

		$PluginModel = ClassRegistry::init('TestPlugin.TestPluginAuthUser');
		$user['id'] = 1;
		$user['username'] = 'gwoo';
		$user['password'] = Security::hash(Configure::read('Security.salt') . 'cake');
		$PluginModel->save($user, false);

		$this->auth->settings['userModel'] = 'TestPlugin.TestPluginAuthUser';
		$this->auth->settings['fields']['username'] = 'username';

		$request = new CakeRequest('posts/index', false);
		$request->data = ['TestPluginAuthUser' => [
			'username' => 'gwoo',
			'password' => 'cake'
		]];

		$result = $this->auth->authenticate($request, $this->response);
		$expected = [
			'id' => 1,
			'username' => 'gwoo',
			'created' => '2007-03-17 01:16:23'
		];
		$this->assertEquals(static::date(), $result['updated']);
		unset($result['updated']);
		$this->assertEquals($expected, $result);
		CakePlugin::unload();
	}

/**
 * test password hasher settings
 *
 * @return void
 */
	public function testPasswordHasherSettings() {
		$this->auth->settings['passwordHasher'] = [
			'className' => 'Simple',
			'hashType' => 'md5'
		];

		$passwordHasher = $this->auth->passwordHasher();
		$result = $passwordHasher->config();
		$this->assertEquals('md5', $result['hashType']);

		$hash = Security::hash('mypass', 'md5', true);
		$User = ClassRegistry::init('User');
		$User->updateAll(
			['password' => $User->getDataSource()->value($hash)],
			['User.user' => 'mariano']
		);

		$request = new CakeRequest('posts/index', false);
		$request->data = ['User' => [
			'user' => 'mariano',
			'password' => 'mypass'
		]];

		$result = $this->auth->authenticate($request, $this->response);
		$expected = [
			'id' => 1,
			'user' => 'mariano',
			'created' => '2007-03-17 01:16:23',
			'updated' => '2007-03-17 01:18:31'
		];
		$this->assertEquals($expected, $result);

		$this->auth = new FormAuthenticate($this->Collection, [
			'fields' => ['username' => 'user', 'password' => 'password'],
			'userModel' => 'User'
		]);
		$this->auth->settings['passwordHasher'] = [
			'className' => 'Simple',
			'hashType' => 'sha1'
		];
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

}
