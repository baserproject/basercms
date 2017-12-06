<?php
/**
 * RouterTest file
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.Test.Case.Routing
 * @since         CakePHP(tm) v 1.2.0.4206
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Router', 'Routing');
App::uses('CakeResponse', 'Network');

if (!defined('FULL_BASE_URL')) {
	define('FULL_BASE_URL', 'http://cakephp.org');
}

/**
 * RouterTest class
 *
 * @package       Cake.Test.Case.Routing
 */
class RouterTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Routing', ['admin' => null, 'prefixes' => []]);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		CakePlugin::unload();
		Router::fullBaseUrl('');
		Configure::write('App.fullBaseUrl', 'http://localhost');
	}

/**
 * testFullBaseUrl method
 *
 * @return void
 */
	public function testFullBaseUrl() {
		$this->assertRegExp('/^http(s)?:\/\//', Router::url('/', true));
		$this->assertRegExp('/^http(s)?:\/\//', Router::url(null, true));
		$this->assertRegExp('/^http(s)?:\/\//', Router::url(['full_base' => true]));
		$this->assertSame(FULL_BASE_URL . '/', Router::url(['full_base' => true]));
	}

/**
 * Tests that the base URL can be changed at runtime.
 *
 * @return void
 */
	public function testBaseUrl() {
		$this->assertEquals(FULL_BASE_URL, Router::fullBaseUrl());
		Router::fullBaseUrl('http://example.com');
		$this->assertEquals('http://example.com/', Router::url('/', true));
		$this->assertEquals('http://example.com', Configure::read('App.fullBaseUrl'));
		Router::fullBaseUrl('https://example.com');
		$this->assertEquals('https://example.com/', Router::url('/', true));
		$this->assertEquals('https://example.com', Configure::read('App.fullBaseUrl'));
	}

/**
 * Test that Router uses App.base to build URL's when there are no stored
 * request objects.
 *
 * @return void
 */
	public function testBaseUrlWithBasePath() {
		Configure::write('App.base', '/cakephp');
		Router::fullBaseUrl('http://example.com');
		$this->assertEquals('http://example.com/cakephp/tasks', Router::url('/tasks', true));
	}

/**
 * testRouteDefaultParams method
 *
 * @return void
 */
	public function testRouteDefaultParams() {
		Router::connect('/:controller', ['controller' => 'posts']);
		$this->assertEquals(Router::url(['action' => 'index']), '/');
	}

/**
 * testMapResources method
 *
 * @return void
 */
	public function testMapResources() {
		$resources = Router::mapResources('Posts');

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Router::parse('/posts');
		$this->assertEquals(['pass' => [], 'named' => [], 'plugin' => '', 'controller' => 'posts', 'action' => 'index', '[method]' => 'GET'], $result);
		$this->assertEquals(['posts'], $resources);

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Router::parse('/posts/13');
		$this->assertEquals(['pass' => ['13'], 'named' => [], 'plugin' => '', 'controller' => 'posts', 'action' => 'view', 'id' => '13', '[method]' => 'GET'], $result);

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$result = Router::parse('/posts');
		$this->assertEquals(['pass' => [], 'named' => [], 'plugin' => '', 'controller' => 'posts', 'action' => 'add', '[method]' => 'POST'], $result);

		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$result = Router::parse('/posts/13');
		$this->assertEquals(['pass' => ['13'], 'named' => [], 'plugin' => '', 'controller' => 'posts', 'action' => 'edit', 'id' => '13', '[method]' => 'PUT'], $result);

		$result = Router::parse('/posts/475acc39-a328-44d3-95fb-015000000000');
		$this->assertEquals(['pass' => ['475acc39-a328-44d3-95fb-015000000000'], 'named' => [], 'plugin' => '', 'controller' => 'posts', 'action' => 'edit', 'id' => '475acc39-a328-44d3-95fb-015000000000', '[method]' => 'PUT'], $result);

		$_SERVER['REQUEST_METHOD'] = 'DELETE';
		$result = Router::parse('/posts/13');
		$this->assertEquals(['pass' => ['13'], 'named' => [], 'plugin' => '', 'controller' => 'posts', 'action' => 'delete', 'id' => '13', '[method]' => 'DELETE'], $result);

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Router::parse('/posts/add');
		$this->assertSame([], $result);

		Router::reload();
		$resources = Router::mapResources('Posts', ['id' => '[a-z0-9_]+']);
		$this->assertEquals(['posts'], $resources);

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Router::parse('/posts/add');
		$this->assertEquals(['pass' => ['add'], 'named' => [], 'plugin' => '', 'controller' => 'posts', 'action' => 'view', 'id' => 'add', '[method]' => 'GET'], $result);

		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$result = Router::parse('/posts/name');
		$this->assertEquals(['pass' => ['name'], 'named' => [], 'plugin' => '', 'controller' => 'posts', 'action' => 'edit', 'id' => 'name', '[method]' => 'PUT'], $result);
	}

/**
 * testMapResources with plugin controllers.
 *
 * @return void
 */
	public function testPluginMapResources() {
		App::build([
			'Plugin' => [
				CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS
			]
		]);
		$resources = Router::mapResources('TestPlugin.TestPlugin');

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Router::parse('/test_plugin/test_plugin');
		$expected = [
			'pass' => [],
			'named' => [],
			'plugin' => 'test_plugin',
			'controller' => 'test_plugin',
			'action' => 'index',
			'[method]' => 'GET'
		];
		$this->assertEquals($expected, $result);
		$this->assertEquals(['test_plugin'], $resources);

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Router::parse('/test_plugin/test_plugin/13');
		$expected = [
			'pass' => ['13'],
			'named' => [],
			'plugin' => 'test_plugin',
			'controller' => 'test_plugin',
			'action' => 'view',
			'id' => '13',
			'[method]' => 'GET'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testMapResources with custom connectOptions
 */
	public function testMapResourcesConnectOptions() {
		App::build([
			'Plugin' => [
				CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS
			]
		]);
		CakePlugin::load('TestPlugin');
		App::uses('TestRoute', 'TestPlugin.Routing/Route');
		Router::mapResources('Posts', [
			'connectOptions' => [
				'routeClass' => 'TestPlugin.TestRoute',
				'foo' => '^(bar)$',
			],
		]);
		$route = end(Router::$routes);
		$this->assertInstanceOf('TestRoute', $route);
		$this->assertEquals('^(bar)$', $route->options['foo']);
	}

/**
 * Test mapResources with a plugin and prefix.
 *
 * @return void
 */
	public function testPluginMapResourcesWithPrefix() {
		App::build([
			'Plugin' => [
				CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS
			]
		]);
		$resources = Router::mapResources('TestPlugin.TestPlugin', ['prefix' => '/api/']);

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Router::parse('/api/test_plugin');
		$expected = [
			'pass' => [],
			'named' => [],
			'plugin' => 'test_plugin',
			'controller' => 'test_plugin',
			'action' => 'index',
			'[method]' => 'GET'
		];
		$this->assertEquals($expected, $result);
		$this->assertEquals(['test_plugin'], $resources);

		Router::mapResources('Posts', ['prefix' => 'api']);

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Router::parse('/api/posts');
		$expected = [
			'pass' => [],
			'named' => [],
			'plugin' => null,
			'controller' => 'posts',
			'action' => 'index',
			'[method]' => 'GET'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testMultipleResourceRoute method
 *
 * @return void
 */
	public function testMultipleResourceRoute() {
		Router::connect('/:controller', ['action' => 'index', '[method]' => ['GET', 'POST']]);

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Router::parse('/posts');
		$this->assertEquals(['pass' => [], 'named' => [], 'plugin' => '', 'controller' => 'posts', 'action' => 'index', '[method]' => ['GET', 'POST']], $result);

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$result = Router::parse('/posts');
		$this->assertEquals(['pass' => [], 'named' => [], 'plugin' => '', 'controller' => 'posts', 'action' => 'index', '[method]' => ['GET', 'POST']], $result);
	}

/**
 * testGenerateUrlResourceRoute method
 *
 * @return void
 */
	public function testGenerateUrlResourceRoute() {
		Router::mapResources('Posts');

		$result = Router::url(['controller' => 'posts', 'action' => 'index', '[method]' => 'GET']);
		$expected = '/posts';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'posts', 'action' => 'view', '[method]' => 'GET', 'id' => 10]);
		$expected = '/posts/10';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'posts', 'action' => 'add', '[method]' => 'POST']);
		$expected = '/posts';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'posts', 'action' => 'edit', '[method]' => 'PUT', 'id' => 10]);
		$expected = '/posts/10';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'posts', 'action' => 'delete', '[method]' => 'DELETE', 'id' => 10]);
		$expected = '/posts/10';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'posts', 'action' => 'edit', '[method]' => 'POST', 'id' => 10]);
		$expected = '/posts/10';
		$this->assertEquals($expected, $result);
	}

/**
 * testUrlNormalization method
 *
 * @return void
 */
	public function testUrlNormalization() {
		$expected = '/users/logout';

		$result = Router::normalize('/users/logout/');
		$this->assertEquals($expected, $result);

		$result = Router::normalize('//users//logout//');
		$this->assertEquals($expected, $result);

		$result = Router::normalize('users/logout');
		$this->assertEquals($expected, $result);

		$result = Router::normalize(['controller' => 'users', 'action' => 'logout']);
		$this->assertEquals($expected, $result);

		$result = Router::normalize('/');
		$this->assertEquals('/', $result);

		$result = Router::normalize('http://google.com/');
		$this->assertEquals('http://google.com/', $result);

		$result = Router::normalize('http://google.com//');
		$this->assertEquals('http://google.com//', $result);

		$result = Router::normalize('/users/login/scope://foo');
		$this->assertEquals('/users/login/scope:/foo', $result);

		$result = Router::normalize('/recipe/recipes/add');
		$this->assertEquals('/recipe/recipes/add', $result);

		$request = new CakeRequest();
		$request->base = '/us';
		Router::setRequestInfo($request);
		$result = Router::normalize('/us/users/logout/');
		$this->assertEquals('/users/logout', $result);

		Router::reload();

		$request = new CakeRequest();
		$request->base = '/cake_12';
		Router::setRequestInfo($request);
		$result = Router::normalize('/cake_12/users/logout/');
		$this->assertEquals('/users/logout', $result);

		Router::reload();
		$_back = Configure::read('App.baseUrl');
		Configure::write('App.baseUrl', '/');

		$request = new CakeRequest();
		$request->base = '/';
		Router::setRequestInfo($request);
		$result = Router::normalize('users/login');
		$this->assertEquals('/users/login', $result);
		Configure::write('App.baseUrl', $_back);

		Router::reload();
		$request = new CakeRequest();
		$request->base = 'beer';
		Router::setRequestInfo($request);
		$result = Router::normalize('beer/admin/beers_tags/add');
		$this->assertEquals('/admin/beers_tags/add', $result);

		$result = Router::normalize('/admin/beers_tags/add');
		$this->assertEquals('/admin/beers_tags/add', $result);
	}

/**
 * test generation of basic URLs.
 *
 * @return void
 */
	public function testUrlGenerationBasic() {
		extract(Router::getNamedExpressions());

		$request = new CakeRequest();
		$request->addParams([
			'action' => 'index', 'plugin' => null, 'controller' => 'subscribe', 'admin' => true
		]);
		$request->base = '/magazine';
		$request->here = '/magazine';
		$request->webroot = '/magazine/';
		Router::setRequestInfo($request);

		$result = Router::url();
		$this->assertEquals('/magazine', $result);

		Router::reload();

		Router::connect('/', ['controller' => 'pages', 'action' => 'display', 'home']);
		$out = Router::url(['controller' => 'pages', 'action' => 'display', 'home']);
		$this->assertEquals('/', $out);

		Router::connect('/pages/*', ['controller' => 'pages', 'action' => 'display']);
		$result = Router::url(['controller' => 'pages', 'action' => 'display', 'about']);
		$expected = '/pages/about';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/:plugin/:id/*', ['controller' => 'posts', 'action' => 'view'], ['id' => $ID]);
		Router::parse('/');

		$result = Router::url(['plugin' => 'cake_plugin', 'controller' => 'posts', 'action' => 'view', 'id' => '1']);
		$expected = '/cake_plugin/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['plugin' => 'cake_plugin', 'controller' => 'posts', 'action' => 'view', 'id' => '1', '0']);
		$expected = '/cake_plugin/1/0';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/:controller/:action/:id', [], ['id' => $ID]);
		Router::parse('/');

		$result = Router::url(['controller' => 'posts', 'action' => 'view', 'id' => '1']);
		$expected = '/posts/view/1';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/:controller/:id', ['action' => 'view']);
		Router::parse('/');

		$result = Router::url(['controller' => 'posts', 'action' => 'view', 'id' => '1']);
		$expected = '/posts/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'posts', 'action' => 'index', '0']);
		$expected = '/posts/index/0';
		$this->assertEquals($expected, $result);

		Router::connect('/view/*', ['controller' => 'posts', 'action' => 'view']);
		Router::promote();
		$result = Router::url(['controller' => 'posts', 'action' => 'view', '1']);
		$expected = '/view/1';
		$this->assertEquals($expected, $result);

		Router::reload();
		$request = new CakeRequest();
		$request->addParams([
			'action' => 'index', 'plugin' => null, 'controller' => 'real_controller_name'
		]);
		$request->base = '/';
		$request->here = '/';
		$request->webroot = '/';
		Router::setRequestInfo($request);

		Router::connect('short_controller_name/:action/*', ['controller' => 'real_controller_name']);
		Router::parse('/');

		$result = Router::url(['controller' => 'real_controller_name', 'page' => '1']);
		$expected = '/short_controller_name/index/page:1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['action' => 'add']);
		$expected = '/short_controller_name/add';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::parse('/');
		$request = new CakeRequest();
		$request->addParams([
			'action' => 'index', 'plugin' => null, 'controller' => 'users', 'url' => ['url' => 'users']
		]);
		$request->base = '/';
		$request->here = '/';
		$request->webroot = '/';
		Router::setRequestInfo($request);

		$result = Router::url(['action' => 'login']);
		$expected = '/users/login';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/page/*', ['plugin' => null, 'controller' => 'pages', 'action' => 'view']);
		Router::parse('/');

		$result = Router::url(['plugin' => 'my_plugin', 'controller' => 'pages', 'action' => 'view', 'my-page']);
		$expected = '/my_plugin/pages/view/my-page';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/contact/:action', ['plugin' => 'contact', 'controller' => 'contact']);
		Router::parse('/');

		$result = Router::url(['plugin' => 'contact', 'controller' => 'contact', 'action' => 'me']);

		$expected = '/contact/me';
		$this->assertEquals($expected, $result);

		Router::reload();
		$request = new CakeRequest();
		$request->addParams([
			'action' => 'index', 'plugin' => 'myplugin', 'controller' => 'mycontroller', 'admin' => false
		]);
		$request->base = '/';
		$request->here = '/';
		$request->webroot = '/';
		Router::setRequestInfo($request);

		$result = Router::url(['plugin' => null, 'controller' => 'myothercontroller']);
		$expected = '/myothercontroller';
		$this->assertEquals($expected, $result);
	}

/**
 * Test that catch all routes work with a variety of falsey inputs.
 *
 * @return void
 */
	public function testUrlCatchAllRoute() {
		Router::connect('/*', ['controller' => 'categories', 'action' => 'index']);
		$result = Router::url(['controller' => 'categories', 'action' => 'index', '0']);
		$this->assertEquals('/0', $result);

		$expected = [
			'plugin' => null,
			'controller' => 'categories',
			'action' => 'index',
			'pass' => ['0'],
			'named' => []
		];
		$result = Router::parse('/0');
		$this->assertEquals($expected, $result);

		$result = Router::parse('0');
		$this->assertEquals($expected, $result);
	}

/**
 * Tests using arrays in named parameters
 *
 * @return void
 */
	public function testArrayNamedParameters() {
		$result = Router::url(['controller' => 'tests', 'pages' => [
			1, 2, 3
		]]);
		$expected = '/tests/index/pages%5B0%5D:1/pages%5B1%5D:2/pages%5B2%5D:3';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'tests',
			'pages' => [
				'param1' => [
					'one',
					'two'
				],
				'three'
			]
		]);
		$expected = '/tests/index/pages%5Bparam1%5D%5B0%5D:one/pages%5Bparam1%5D%5B1%5D:two/pages%5B0%5D:three';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'tests',
			'pages' => [
				'param1' => [
					'one' => 1,
					'two' => 2
				],
				'three'
			]
		]);
		$expected = '/tests/index/pages%5Bparam1%5D%5Bone%5D:1/pages%5Bparam1%5D%5Btwo%5D:2/pages%5B0%5D:three';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'tests',
			'super' => [
				'nested' => [
					'array' => 'awesome',
					'something' => 'else'
				],
				'cool'
			]
		]);
		$expected = '/tests/index/super%5Bnested%5D%5Barray%5D:awesome/super%5Bnested%5D%5Bsomething%5D:else/super%5B0%5D:cool';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'tests', 'namedParam' => [
			'keyed' => 'is an array',
			'test'
		]]);
		$expected = '/tests/index/namedParam%5Bkeyed%5D:is%20an%20array/namedParam%5B0%5D:test';
		$this->assertEquals($expected, $result);
	}

/**
 * Test generation of routes with query string parameters.
 *
 * @return void
 */
	public function testUrlGenerationWithQueryStrings() {
		$result = Router::url(['controller' => 'posts', 'action' => 'index', '0', '?' => 'var=test&var2=test2']);
		$expected = '/posts/index/0?var=test&var2=test2';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'posts', '0', '?' => 'var=test&var2=test2']);
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'posts', '0', '?' => ['var' => 'test', 'var2' => 'test2']]);
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'posts', '0', '?' => ['var' => null]]);
		$this->assertEquals('/posts/index/0', $result);

		$result = Router::url(['controller' => 'posts', '0', '?' => 'var=test&var2=test2', '#' => 'unencoded string %']);
		$expected = '/posts/index/0?var=test&var2=test2#unencoded string %';
		$this->assertEquals($expected, $result);
	}

/**
 * test that regex validation of keyed route params is working.
 *
 * @return void
 */
	public function testUrlGenerationWithRegexQualifiedParams() {
		Router::connect(
			':language/galleries',
			['controller' => 'galleries', 'action' => 'index'],
			['language' => '[a-z]{3}']
		);

		Router::connect(
			'/:language/:admin/:controller/:action/*',
			['admin' => 'admin'],
			['language' => '[a-z]{3}', 'admin' => 'admin']
		);

		Router::connect('/:language/:controller/:action/*',
			[],
			['language' => '[a-z]{3}']
		);

		$result = Router::url(['admin' => false, 'language' => 'dan', 'action' => 'index', 'controller' => 'galleries']);
		$expected = '/dan/galleries';
		$this->assertEquals($expected, $result);

		$result = Router::url(['admin' => false, 'language' => 'eng', 'action' => 'index', 'controller' => 'galleries']);
		$expected = '/eng/galleries';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/:language/pages',
			['controller' => 'pages', 'action' => 'index'],
			['language' => '[a-z]{3}']
		);
		Router::connect('/:language/:controller/:action/*', [], ['language' => '[a-z]{3}']);

		$result = Router::url(['language' => 'eng', 'action' => 'index', 'controller' => 'pages']);
		$expected = '/eng/pages';
		$this->assertEquals($expected, $result);

		$result = Router::url(['language' => 'eng', 'controller' => 'pages']);
		$this->assertEquals($expected, $result);

		$result = Router::url(['language' => 'eng', 'controller' => 'pages', 'action' => 'add']);
		$expected = '/eng/pages/add';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/forestillinger/:month/:year/*',
			['plugin' => 'shows', 'controller' => 'shows', 'action' => 'calendar'],
			['month' => '0[1-9]|1[012]', 'year' => '[12][0-9]{3}']
		);
		Router::parse('/');

		$result = Router::url(['plugin' => 'shows', 'controller' => 'shows', 'action' => 'calendar', 'month' => 10, 'year' => 2007, 'min-forestilling']);
		$expected = '/forestillinger/10/2007/min-forestilling';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/kalender/:month/:year/*',
			['plugin' => 'shows', 'controller' => 'shows', 'action' => 'calendar'],
			['month' => '0[1-9]|1[012]', 'year' => '[12][0-9]{3}']
		);
		Router::connect('/kalender/*', ['plugin' => 'shows', 'controller' => 'shows', 'action' => 'calendar']);
		Router::parse('/');

		$result = Router::url(['plugin' => 'shows', 'controller' => 'shows', 'action' => 'calendar', 'min-forestilling']);
		$expected = '/kalender/min-forestilling';
		$this->assertEquals($expected, $result);

		$result = Router::url(['plugin' => 'shows', 'controller' => 'shows', 'action' => 'calendar', 'year' => 2007, 'month' => 10, 'min-forestilling']);
		$expected = '/kalender/10/2007/min-forestilling';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/:controller/:action/*', [], [
			'controller' => 'source|wiki|commits|tickets|comments|view',
			'action' => 'branches|history|branch|logs|view|start|add|edit|modify'
		]);
	}

/**
 * Test URL generation with an admin prefix
 *
 * @return void
 */
	public function testUrlGenerationWithAdminPrefix() {
		Configure::write('Routing.prefixes', ['admin']);
		Router::reload();

		Router::connectNamed(['event', 'lang']);
		Router::connect('/', ['controller' => 'pages', 'action' => 'display', 'home']);
		Router::connect('/pages/contact_us', ['controller' => 'pages', 'action' => 'contact_us']);
		Router::connect('/pages/*', ['controller' => 'pages', 'action' => 'display']);
		Router::connect('/reset/*', ['admin' => true, 'controller' => 'users', 'action' => 'reset']);
		Router::connect('/tests', ['controller' => 'tests', 'action' => 'index']);
		Router::parseExtensions('rss');

		$request = new CakeRequest();
		$request->addParams([
			'controller' => 'registrations', 'action' => 'admin_index',
			'plugin' => null, 'prefix' => 'admin', 'admin' => true,
			'ext' => 'html'
		]);
		$request->base = '';
		$request->here = '/admin/registrations/index';
		$request->webroot = '/';
		Router::setRequestInfo($request);

		$result = Router::url(['page' => 2]);
		$expected = '/admin/registrations/index/page:2';
		$this->assertEquals($expected, $result);

		Router::reload();
		$request = new CakeRequest();
		$request->addParams([
			'controller' => 'subscriptions', 'action' => 'admin_index',
			'plugin' => null, 'admin' => true,
			'url' => ['url' => 'admin/subscriptions/index/page:2']
		]);
		$request->base = '/magazine';
		$request->here = '/magazine/admin/subscriptions/index/page:2';
		$request->webroot = '/magazine/';
		Router::setRequestInfo($request);

		Router::parse('/');

		$result = Router::url(['page' => 3]);
		$expected = '/magazine/admin/subscriptions/index/page:3';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/admin/subscriptions/:action/*', ['controller' => 'subscribe', 'admin' => true, 'prefix' => 'admin']);
		Router::parse('/');

		$request = new CakeRequest();
		$request->addParams([
			'action' => 'admin_index', 'plugin' => null, 'controller' => 'subscribe',
			'admin' => true, 'url' => ['url' => 'admin/subscriptions/edit/1']
		]);
		$request->base = '/magazine';
		$request->here = '/magazine/admin/subscriptions/edit/1';
		$request->webroot = '/magazine/';
		Router::setRequestInfo($request);

		$result = Router::url(['action' => 'edit', 1]);
		$expected = '/magazine/admin/subscriptions/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['admin' => true, 'controller' => 'users', 'action' => 'login']);
		$expected = '/magazine/admin/users/login';
		$this->assertEquals($expected, $result);

		Router::reload();
		$request = new CakeRequest();
		$request->addParams([
			'admin' => true, 'action' => 'index', 'plugin' => null, 'controller' => 'users',
			'url' => ['url' => 'users']
		]);
		$request->base = '/';
		$request->here = '/';
		$request->webroot = '/';
		Router::setRequestInfo($request);

		Router::connect('/page/*', ['controller' => 'pages', 'action' => 'view', 'admin' => true, 'prefix' => 'admin']);
		Router::parse('/');

		$result = Router::url(['admin' => true, 'controller' => 'pages', 'action' => 'view', 'my-page']);
		$expected = '/page/my-page';
		$this->assertEquals($expected, $result);

		Router::reload();

		$request = new CakeRequest();
		$request->addParams([
			'plugin' => null, 'controller' => 'pages', 'action' => 'admin_add', 'prefix' => 'admin', 'admin' => true,
			'url' => ['url' => 'admin/pages/add']
		]);
		$request->base = '';
		$request->here = '/admin/pages/add';
		$request->webroot = '/';
		Router::setRequestInfo($request);
		Router::parse('/');

		$result = Router::url(['plugin' => null, 'controller' => 'pages', 'action' => 'add', 'id' => false]);
		$expected = '/admin/pages/add';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::parse('/');
		$request = new CakeRequest();
		$request->addParams([
			'plugin' => null, 'controller' => 'pages', 'action' => 'admin_add', 'prefix' => 'admin', 'admin' => true,
			'url' => ['url' => 'admin/pages/add']
		]);
		$request->base = '';
		$request->here = '/admin/pages/add';
		$request->webroot = '/';
		Router::setRequestInfo($request);

		$result = Router::url(['plugin' => null, 'controller' => 'pages', 'action' => 'add', 'id' => false]);
		$expected = '/admin/pages/add';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/admin/:controller/:action/:id', ['admin' => true], ['id' => '[0-9]+']);
		Router::parse('/');
		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'pages', 'action' => 'admin_edit', 'pass' => ['284'],
				'prefix' => 'admin', 'admin' => true,
				'url' => ['url' => 'admin/pages/edit/284']
			])->addPaths([
				'base' => '', 'here' => '/admin/pages/edit/284', 'webroot' => '/'
			])
		);

		$result = Router::url(['plugin' => null, 'controller' => 'pages', 'action' => 'edit', 'id' => '284']);
		$expected = '/admin/pages/edit/284';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::parse('/');

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'pages', 'action' => 'admin_add', 'prefix' => 'admin',
				'admin' => true, 'url' => ['url' => 'admin/pages/add']
			])->addPaths([
				'base' => '', 'here' => '/admin/pages/add', 'webroot' => '/'
			])
		);

		$result = Router::url(['plugin' => null, 'controller' => 'pages', 'action' => 'add', 'id' => false]);
		$expected = '/admin/pages/add';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::parse('/');

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'pages', 'action' => 'admin_edit', 'prefix' => 'admin',
				'admin' => true, 'pass' => ['284'], 'url' => ['url' => 'admin/pages/edit/284']
			])->addPaths([
				'base' => '', 'here' => '/admin/pages/edit/284', 'webroot' => '/'
			])
		);

		$result = Router::url(['plugin' => null, 'controller' => 'pages', 'action' => 'edit', 284]);
		$expected = '/admin/pages/edit/284';
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/admin/posts/*', ['controller' => 'posts', 'action' => 'index', 'admin' => true]);
		Router::parse('/');
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'posts', 'action' => 'admin_index', 'prefix' => 'admin',
				'admin' => true, 'pass' => ['284'], 'url' => ['url' => 'admin/posts']
			])->addPaths([
				'base' => '', 'here' => '/admin/posts', 'webroot' => '/'
			])
		);

		$result = Router::url(['all']);
		$expected = '/admin/posts/all';
		$this->assertEquals($expected, $result);
	}

/**
 * testUrlGenerationWithExtensions method
 *
 * @return void
 */
	public function testUrlGenerationWithExtensions() {
		Router::parse('/');
		$result = Router::url(['plugin' => null, 'controller' => 'articles', 'action' => 'add', 'id' => null, 'ext' => 'json']);
		$expected = '/articles/add.json';
		$this->assertEquals($expected, $result);

		$result = Router::url(['plugin' => null, 'controller' => 'articles', 'action' => 'add', 'ext' => 'json']);
		$expected = '/articles/add.json';
		$this->assertEquals($expected, $result);

		$result = Router::url(['plugin' => null, 'controller' => 'articles', 'action' => 'index', 'id' => null, 'ext' => 'json']);
		$expected = '/articles.json';
		$this->assertEquals($expected, $result);

		$result = Router::url(['plugin' => null, 'controller' => 'articles', 'action' => 'index', 'ext' => 'json']);
		$expected = '/articles.json';
		$this->assertEquals($expected, $result);
	}

/**
 * testPluginUrlGeneration method
 *
 * @return void
 */
	public function testUrlGenerationPlugins() {
		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => 'test', 'controller' => 'controller', 'action' => 'index'
			])->addPaths([
				'base' => '/base', 'here' => '/clients/sage/portal/donations', 'webroot' => '/base/'
			])
		);

		$this->assertEquals(Router::url('read/1'), '/base/test/controller/read/1');

		Router::reload();
		Router::connect('/:lang/:plugin/:controller/*', ['action' => 'index']);

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'lang' => 'en',
				'plugin' => 'shows', 'controller' => 'shows', 'action' => 'index',
				'url' => ['url' => 'en/shows/'],
			])->addPaths([
				'base' => '', 'here' => '/en/shows', 'webroot' => '/'
			])
		);

		Router::parse('/en/shows/');

		$result = Router::url([
			'lang' => 'en',
			'controller' => 'shows', 'action' => 'index', 'page' => '1',
		]);
		$expected = '/en/shows/shows/page:1';
		$this->assertEquals($expected, $result);
	}

/**
 * test that you can leave active plugin routes with plugin = null
 *
 * @return void
 */
	public function testCanLeavePlugin() {
		Router::reload();
		Router::connect(
			'/admin/other/:controller/:action/*',
			[
				'admin' => 1,
				'plugin' => 'aliased',
				'prefix' => 'admin'
			]
		);
		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'pass' => [],
				'admin' => true,
				'prefix' => 'admin',
				'plugin' => 'this',
				'action' => 'admin_index',
				'controller' => 'interesting',
				'url' => ['url' => 'admin/this/interesting/index'],
			])->addPaths([
				'base' => '',
				'here' => '/admin/this/interesting/index',
				'webroot' => '/',
			])
		);
		$result = Router::url(['plugin' => null, 'controller' => 'posts', 'action' => 'index']);
		$this->assertEquals('/admin/posts', $result);

		$result = Router::url(['controller' => 'posts', 'action' => 'index']);
		$this->assertEquals('/admin/this/posts', $result);

		$result = Router::url(['plugin' => 'aliased', 'controller' => 'posts', 'action' => 'index']);
		$this->assertEquals('/admin/other/posts/index', $result);
	}

/**
 * Test that URL's fail to parse when they are prefixed with //
 *
 * @return void
 */
	public function testUrlParseFailureDoubleSlash() {
		Router::connect('/posts', ['controller' => 'posts', 'action' => 'index']);
		$result = Router::parse('/posts');
		$this->assertEquals(
			['pass' => [], 'named' => [], 'plugin' => null, 'controller' => 'posts', 'action' => 'index'],
			$result
		);

		$result = Router::parse('//posts');
		$this->assertEquals([], $result);
	}

/**
 * testUrlParsing method
 *
 * @return void
 */
	public function testUrlParsing() {
		extract(Router::getNamedExpressions());

		Router::connect('/posts/:value/:somevalue/:othervalue/*', ['controller' => 'posts', 'action' => 'view'], ['value', 'somevalue', 'othervalue']);
		$result = Router::parse('/posts/2007/08/01/title-of-post-here');
		$expected = ['value' => '2007', 'somevalue' => '08', 'othervalue' => '01', 'controller' => 'posts', 'action' => 'view', 'plugin' => '', 'pass' => ['0' => 'title-of-post-here'], 'named' => []];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/posts/:year/:month/:day/*', ['controller' => 'posts', 'action' => 'view'], ['year' => $Year, 'month' => $Month, 'day' => $Day]);
		$result = Router::parse('/posts/2007/08/01/title-of-post-here');
		$expected = ['year' => '2007', 'month' => '08', 'day' => '01', 'controller' => 'posts', 'action' => 'view', 'plugin' => '', 'pass' => ['0' => 'title-of-post-here'], 'named' => []];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/posts/:day/:year/:month/*', ['controller' => 'posts', 'action' => 'view'], ['year' => $Year, 'month' => $Month, 'day' => $Day]);
		$result = Router::parse('/posts/01/2007/08/title-of-post-here');
		$expected = ['day' => '01', 'year' => '2007', 'month' => '08', 'controller' => 'posts', 'action' => 'view', 'plugin' => '', 'pass' => ['0' => 'title-of-post-here'], 'named' => []];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/posts/:month/:day/:year/*', ['controller' => 'posts', 'action' => 'view'], ['year' => $Year, 'month' => $Month, 'day' => $Day]);
		$result = Router::parse('/posts/08/01/2007/title-of-post-here');
		$expected = ['month' => '08', 'day' => '01', 'year' => '2007', 'controller' => 'posts', 'action' => 'view', 'plugin' => '', 'pass' => ['0' => 'title-of-post-here'], 'named' => []];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/posts/:year/:month/:day/*', ['controller' => 'posts', 'action' => 'view']);
		$result = Router::parse('/posts/2007/08/01/title-of-post-here');
		$expected = ['year' => '2007', 'month' => '08', 'day' => '01', 'controller' => 'posts', 'action' => 'view', 'plugin' => '', 'pass' => ['0' => 'title-of-post-here'], 'named' => []];
		$this->assertEquals($expected, $result);

		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';
		$result = Router::parse('/pages/display/home');
		$expected = ['plugin' => null, 'pass' => ['home'], 'controller' => 'pages', 'action' => 'display', 'named' => []];
		$this->assertEquals($expected, $result);

		$result = Router::parse('pages/display/home/');
		$this->assertEquals($expected, $result);

		$result = Router::parse('pages/display/home');
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/page/*', ['controller' => 'test']);
		$result = Router::parse('/page/my-page');
		$expected = ['pass' => ['my-page'], 'plugin' => null, 'controller' => 'test', 'action' => 'index', 'named' => []];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/:language/contact', ['language' => 'eng', 'plugin' => 'contact', 'controller' => 'contact', 'action' => 'index'], ['language' => '[a-z]{3}']);
		$result = Router::parse('/eng/contact');
		$expected = ['pass' => [], 'named' => [], 'language' => 'eng', 'plugin' => 'contact', 'controller' => 'contact', 'action' => 'index'];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/forestillinger/:month/:year/*',
			['plugin' => 'shows', 'controller' => 'shows', 'action' => 'calendar'],
			['month' => '0[1-9]|1[012]', 'year' => '[12][0-9]{3}']
		);

		$result = Router::parse('/forestillinger/10/2007/min-forestilling');
		$expected = ['pass' => ['min-forestilling'], 'plugin' => 'shows', 'controller' => 'shows', 'action' => 'calendar', 'year' => 2007, 'month' => 10, 'named' => []];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/:controller/:action/*');
		Router::connect('/', ['plugin' => 'pages', 'controller' => 'pages', 'action' => 'display']);
		$result = Router::parse('/');
		$expected = ['pass' => [], 'named' => [], 'controller' => 'pages', 'action' => 'display', 'plugin' => 'pages'];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/posts/edit/0');
		$expected = ['pass' => [0], 'named' => [], 'controller' => 'posts', 'action' => 'edit', 'plugin' => null];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/posts/:id::url_title', ['controller' => 'posts', 'action' => 'view'], ['pass' => ['id', 'url_title'], 'id' => '[\d]+']);
		$result = Router::parse('/posts/5:sample-post-title');
		$expected = ['pass' => ['5', 'sample-post-title'], 'named' => [], 'id' => 5, 'url_title' => 'sample-post-title', 'plugin' => null, 'controller' => 'posts', 'action' => 'view'];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/posts/:id::url_title/*', ['controller' => 'posts', 'action' => 'view'], ['pass' => ['id', 'url_title'], 'id' => '[\d]+']);
		$result = Router::parse('/posts/5:sample-post-title/other/params/4');
		$expected = ['pass' => ['5', 'sample-post-title', 'other', 'params', '4'], 'named' => [], 'id' => 5, 'url_title' => 'sample-post-title', 'plugin' => null, 'controller' => 'posts', 'action' => 'view'];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/posts/:url_title-(uuid::id)', ['controller' => 'posts', 'action' => 'view'], ['pass' => ['id', 'url_title'], 'id' => $UUID]);
		$result = Router::parse('/posts/sample-post-title-(uuid:47fc97a9-019c-41d1-a058-1fa3cbdd56cb)');
		$expected = ['pass' => ['47fc97a9-019c-41d1-a058-1fa3cbdd56cb', 'sample-post-title'], 'named' => [], 'id' => '47fc97a9-019c-41d1-a058-1fa3cbdd56cb', 'url_title' => 'sample-post-title', 'plugin' => null, 'controller' => 'posts', 'action' => 'view'];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/posts/view/*', ['controller' => 'posts', 'action' => 'view'], ['named' => false]);
		$result = Router::parse('/posts/view/foo:bar/routing:fun');
		$expected = ['pass' => ['foo:bar', 'routing:fun'], 'named' => [], 'plugin' => null, 'controller' => 'posts', 'action' => 'view'];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/posts/view/*', ['controller' => 'posts', 'action' => 'view'], ['named' => ['foo', 'answer']]);
		$result = Router::parse('/posts/view/foo:bar/routing:fun/answer:42');
		$expected = ['pass' => ['routing:fun'], 'named' => ['foo' => 'bar', 'answer' => '42'], 'plugin' => null, 'controller' => 'posts', 'action' => 'view'];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/posts/view/*', ['controller' => 'posts', 'action' => 'view'], ['named' => ['foo', 'answer'], 'greedyNamed' => true]);
		$result = Router::parse('/posts/view/foo:bar/routing:fun/answer:42');
		$expected = ['pass' => [], 'named' => ['foo' => 'bar', 'routing' => 'fun', 'answer' => '42'], 'plugin' => null, 'controller' => 'posts', 'action' => 'view'];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/posts/view/*', ['controller' => 'posts', 'action' => 'view'], ['named' => ['foo', 'answer'], 'greedyNamed' => true]);
		$result = Router::parse('/posts/view/foo:bar/routing:fun/answer:42?id=123&tab=abc');
		$expected = ['pass' => [], 'named' => ['foo' => 'bar', 'routing' => 'fun', 'answer' => '42'], 'plugin' => null, 'controller' => 'posts', 'action' => 'view', '?' => ['id' => '123', 'tab' => 'abc']];
		$this->assertEquals($expected, $result);
	}

/**
 * test that the persist key works.
 *
 * @return void
 */
	public function testPersistentParameters() {
		Router::reload();
		Router::connect(
			'/:lang/:color/posts/view/*',
			['controller' => 'posts', 'action' => 'view'],
			['persist' => ['lang', 'color']]
		);
		Router::connect(
			'/:lang/:color/posts/index',
			['controller' => 'posts', 'action' => 'index'],
			['persist' => ['lang']]
		);
		Router::connect('/:lang/:color/posts/edit/*', ['controller' => 'posts', 'action' => 'edit']);
		Router::connect('/about', ['controller' => 'pages', 'action' => 'view', 'about']);
		Router::parse('/en/red/posts/view/5');

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'lang' => 'en',
				'color' => 'red',
				'prefix' => 'admin',
				'plugin' => null,
				'action' => 'view',
				'controller' => 'posts',
			])->addPaths([
				'base' => '/',
				'here' => '/en/red/posts/view/5',
				'webroot' => '/',
			])
		);
		$expected = '/en/red/posts/view/6';
		$result = Router::url(['controller' => 'posts', 'action' => 'view', 6]);
		$this->assertEquals($expected, $result);

		$expected = '/en/blue/posts/index';
		$result = Router::url(['controller' => 'posts', 'action' => 'index', 'color' => 'blue']);
		$this->assertEquals($expected, $result);

		$expected = '/posts/edit/6';
		$result = Router::url(['controller' => 'posts', 'action' => 'edit', 6, 'color' => null, 'lang' => null]);
		$this->assertEquals($expected, $result);

		$expected = '/posts';
		$result = Router::url(['controller' => 'posts', 'action' => 'index']);
		$this->assertEquals($expected, $result);

		$expected = '/posts/edit/7';
		$result = Router::url(['controller' => 'posts', 'action' => 'edit', 7]);
		$this->assertEquals($expected, $result);

		$expected = '/about';
		$result = Router::url(['controller' => 'pages', 'action' => 'view', 'about']);
		$this->assertEquals($expected, $result);
	}

/**
 * testUuidRoutes method
 *
 * @return void
 */
	public function testUuidRoutes() {
		Router::connect(
			'/subjects/add/:category_id',
			['controller' => 'subjects', 'action' => 'add'],
			['category_id' => '\w{8}-\w{4}-\w{4}-\w{4}-\w{12}']
		);
		$result = Router::parse('/subjects/add/4795d601-19c8-49a6-930e-06a8b01d17b7');
		$expected = ['pass' => [], 'named' => [], 'category_id' => '4795d601-19c8-49a6-930e-06a8b01d17b7', 'plugin' => null, 'controller' => 'subjects', 'action' => 'add'];
		$this->assertEquals($expected, $result);
	}

/**
 * testRouteSymmetry method
 *
 * @return void
 */
	public function testRouteSymmetry() {
		Router::connect(
			"/:extra/page/:slug/*",
			['controller' => 'pages', 'action' => 'view', 'extra' => null],
			["extra" => '[a-z1-9_]*', "slug" => '[a-z1-9_]+', "action" => 'view']
		);

		$result = Router::parse('/some_extra/page/this_is_the_slug');
		$expected = ['pass' => [], 'named' => [], 'plugin' => null, 'controller' => 'pages', 'action' => 'view', 'slug' => 'this_is_the_slug', 'extra' => 'some_extra'];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/page/this_is_the_slug');
		$expected = ['pass' => [], 'named' => [], 'plugin' => null, 'controller' => 'pages', 'action' => 'view', 'slug' => 'this_is_the_slug', 'extra' => null];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect(
			"/:extra/page/:slug/*",
			['controller' => 'pages', 'action' => 'view', 'extra' => null],
			["extra" => '[a-z1-9_]*', "slug" => '[a-z1-9_]+']
		);
		Router::parse('/');

		$result = Router::url(['admin' => null, 'plugin' => null, 'controller' => 'pages', 'action' => 'view', 'slug' => 'this_is_the_slug', 'extra' => null]);
		$expected = '/page/this_is_the_slug';
		$this->assertEquals($expected, $result);

		$result = Router::url(['admin' => null, 'plugin' => null, 'controller' => 'pages', 'action' => 'view', 'slug' => 'this_is_the_slug', 'extra' => 'some_extra']);
		$expected = '/some_extra/page/this_is_the_slug';
		$this->assertEquals($expected, $result);
	}

/**
 * Test parse and reverse symmetry
 *
 * @return void
 * @dataProvider parseReverseSymmetryData
 */
	public function testParseReverseSymmetry($url) {
		$this->assertSame($url, Router::reverse(Router::parse($url) + ['url' => []]));
	}

/**
 * Data for parse and reverse test
 *
 * @return array
 */
	public function parseReverseSymmetryData() {
		return [
			['/'],
			['/controller/action'],
			['/controller/action/param'],
			['/controller/action?param1=value1&param2=value2'],
			['/controller/action/param?param1=value1'],
			['/controller/action/named1:nv1'],
			['/controller/action/named1:nv1?param1=value1']
		];
	}

/**
 * Test that Routing.prefixes are used when a Router instance is created
 * or reset
 *
 * @return void
 */
	public function testRoutingPrefixesSetting() {
		$restore = Configure::read('Routing');

		Configure::write('Routing.prefixes', ['admin', 'member', 'super_user']);
		Router::reload();
		$result = Router::prefixes();
		$expected = ['admin', 'member', 'super_user'];
		$this->assertEquals($expected, $result);

		Configure::write('Routing.prefixes', ['admin', 'member']);
		Router::reload();
		$result = Router::prefixes();
		$expected = ['admin', 'member'];
		$this->assertEquals($expected, $result);

		Configure::write('Routing', $restore);
	}

/**
 * Test prefix routing and plugin combinations
 *
 * @return void
 */
	public function testPrefixRoutingAndPlugins() {
		Configure::write('Routing.prefixes', ['admin']);
		$paths = App::path('plugins');
		App::build([
			'plugins' => [
				CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS
			]
		], App::RESET);
		CakePlugin::load(['TestPlugin']);

		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';
		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'admin' => true, 'controller' => 'controller', 'action' => 'action',
				'plugin' => null, 'prefix' => 'admin'
			])->addPaths([
				'base' => '/',
				'here' => '/',
				'webroot' => '/base/',
			])
		);
		Router::parse('/');

		$result = Router::url(['plugin' => 'test_plugin', 'controller' => 'test_plugin', 'action' => 'index']);
		$expected = '/admin/test_plugin';
		$this->assertEquals($expected, $result);

		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';
		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => 'test_plugin', 'controller' => 'show_tickets', 'action' => 'admin_edit',
				'pass' => ['6'], 'prefix' => 'admin', 'admin' => true, 'form' => [],
				'url' => ['url' => 'admin/shows/show_tickets/edit/6']
			])->addPaths([
				'base' => '/',
				'here' => '/admin/shows/show_tickets/edit/6',
				'webroot' => '/',
			])
		);

		$result = Router::url([
			'plugin' => 'test_plugin', 'controller' => 'show_tickets', 'action' => 'edit', 6,
			'admin' => true, 'prefix' => 'admin'
		]);
		$expected = '/admin/test_plugin/show_tickets/edit/6';
		$this->assertEquals($expected, $result);

		$result = Router::url([
			'plugin' => 'test_plugin', 'controller' => 'show_tickets', 'action' => 'index', 'admin' => true
		]);
		$expected = '/admin/test_plugin/show_tickets';
		$this->assertEquals($expected, $result);

		App::build(['plugins' => $paths]);
	}

/**
 * testParseExtensions method
 *
 * @return void
 */
	public function testParseExtensions() {
		$this->assertEquals([], Router::extensions());

		Router::parseExtensions('rss');
		$this->assertEquals(['rss'], Router::extensions());
	}

/**
 * testSetExtensions method
 *
 * @return void
 */
	public function testSetExtensions() {
		Router::setExtensions(['rss']);
		$this->assertEquals(['rss'], Router::extensions());

		require CAKE . 'Config' . DS . 'routes.php';
		$result = Router::parse('/posts.rss');
		$this->assertFalse(isset($result['ext']));

		Router::parseExtensions();
		$result = Router::parse('/posts.rss');
		$this->assertEquals('rss', $result['ext']);

		$result = Router::parse('/posts.xml');
		$this->assertFalse(isset($result['ext']));

		Router::setExtensions(['xml']);
		$result = Router::extensions();
		$this->assertEquals(['rss', 'xml'], $result);

		$result = Router::parse('/posts.xml');
		$this->assertEquals('xml', $result['ext']);

		$result = Router::setExtensions(['pdf'], false);
		$this->assertEquals(['pdf'], $result);
	}

/**
 * testExtensionParsing method
 *
 * @return void
 */
	public function testExtensionParsing() {
		Router::parseExtensions();
		require CAKE . 'Config' . DS . 'routes.php';

		$result = Router::parse('/posts.rss');
		$expected = ['plugin' => null, 'controller' => 'posts', 'action' => 'index', 'ext' => 'rss', 'pass' => [], 'named' => []];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/posts/view/1.rss');
		$expected = ['plugin' => null, 'controller' => 'posts', 'action' => 'view', 'pass' => ['1'], 'named' => [], 'ext' => 'rss'];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/posts/view/1.rss?query=test');
		$expected['?'] = ['query' => 'test'];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/posts/view/1.atom');
		unset($expected['?']);
		$expected['ext'] = 'atom';
		$this->assertEquals($expected, $result);

		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';

		Router::parseExtensions('rss', 'xml');

		$result = Router::parse('/posts.xml');
		$expected = ['plugin' => null, 'controller' => 'posts', 'action' => 'index', 'ext' => 'xml', 'pass' => [], 'named' => []];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/posts.atom?hello=goodbye');
		$expected = ['plugin' => null, 'controller' => 'posts.atom', 'action' => 'index', 'pass' => [], 'named' => [], '?' => ['hello' => 'goodbye']];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/controller/action', ['controller' => 'controller', 'action' => 'action', 'ext' => 'rss']);
		$result = Router::parse('/controller/action');
		$expected = ['controller' => 'controller', 'action' => 'action', 'plugin' => null, 'ext' => 'rss', 'named' => [], 'pass' => []];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::parseExtensions('rss');
		Router::connect('/controller/action', ['controller' => 'controller', 'action' => 'action', 'ext' => 'rss']);
		$result = Router::parse('/controller/action');
		$expected = ['controller' => 'controller', 'action' => 'action', 'plugin' => null, 'ext' => 'rss', 'named' => [], 'pass' => []];
		$this->assertEquals($expected, $result);
	}

/**
 * testQuerystringGeneration method
 *
 * @return void
 */
	public function testQuerystringGeneration() {
		$result = Router::url(['controller' => 'posts', 'action' => 'index', '0', '?' => 'var=test&var2=test2']);
		$expected = '/posts/index/0?var=test&var2=test2';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'posts', 'action' => 'index', '0', '?' => ['var' => 'test', 'var2' => 'test2']]);
		$this->assertEquals($expected, $result);

		$expected .= '&more=test+data';
		$result = Router::url(['controller' => 'posts', 'action' => 'index', '0', '?' => ['var' => 'test', 'var2' => 'test2', 'more' => 'test data']]);
		$this->assertEquals($expected, $result);

		// Test bug #4614
		$restore = ini_get('arg_separator.output');
		ini_set('arg_separator.output', '&amp;');
		$result = Router::url(['controller' => 'posts', 'action' => 'index', '0', '?' => ['var' => 'test', 'var2' => 'test2', 'more' => 'test data']]);
		$this->assertEquals($expected, $result);
		ini_set('arg_separator.output', $restore);

		$result = Router::url(['controller' => 'posts', 'action' => 'index', '0', '?' => ['var' => 'test', 'var2' => 'test2']], ['escape' => true]);
		$expected = '/posts/index/0?var=test&amp;var2=test2';
		$this->assertEquals($expected, $result);
	}

/**
 * testConnectNamed method
 *
 * @return void
 */
	public function testConnectNamed() {
		$named = Router::connectNamed(false, ['default' => true]);
		$this->assertFalse($named['greedyNamed']);
		$this->assertEquals(array_keys($named['rules']), $named['default']);

		Router::reload();
		Router::connect('/foo/*', ['controller' => 'bar', 'action' => 'fubar']);
		Router::connectNamed([], ['separator' => '=']);
		$result = Router::parse('/foo/param1=value1/param2=value2');
		$expected = ['pass' => [], 'named' => ['param1' => 'value1', 'param2' => 'value2'], 'controller' => 'bar', 'action' => 'fubar', 'plugin' => null];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/controller/action/*', ['controller' => 'controller', 'action' => 'action'], ['named' => ['param1' => 'value[\d]']]);
		Router::connectNamed([], ['greedy' => false, 'separator' => '=']);
		$result = Router::parse('/controller/action/param1=value1/param2=value2');
		$expected = ['pass' => ['param2=value2'], 'named' => ['param1' => 'value1'], 'controller' => 'controller', 'action' => 'action', 'plugin' => null];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/:controller/:action/*');
		Router::connectNamed(['page'], ['default' => false, 'greedy' => false]);
		$result = Router::parse('/categories/index/limit=5');
		$this->assertTrue(empty($result['named']));
	}

/**
 * testNamedArgsUrlGeneration method
 *
 * @return void
 */
	public function testNamedArgsUrlGeneration() {
		$result = Router::url(['controller' => 'posts', 'action' => 'index', 'published' => 1, 'deleted' => 1]);
		$expected = '/posts/index/published:1/deleted:1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'posts', 'action' => 'index', 'published' => 0, 'deleted' => 0]);
		$expected = '/posts/index/published:0/deleted:0';
		$this->assertEquals($expected, $result);

		Router::reload();
		extract(Router::getNamedExpressions());
		Router::connectNamed(['file' => '[\w\.\-]+\.(html|png)']);
		Router::connect('/', ['controller' => 'graphs', 'action' => 'index']);
		Router::connect('/:id/*', ['controller' => 'graphs', 'action' => 'view'], ['id' => $ID]);

		$result = Router::url(['controller' => 'graphs', 'action' => 'view', 'id' => 12, 'file' => 'asdf.png']);
		$expected = '/12/file:asdf.png';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'graphs', 'action' => 'view', 12, 'file' => 'asdf.foo']);
		$expected = '/graphs/view/12/file:asdf.foo';
		$this->assertEquals($expected, $result);

		Configure::write('Routing.prefixes', ['admin']);

		Router::reload();
		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'admin' => true, 'controller' => 'controller', 'action' => 'index', 'plugin' => null
			])->addPaths([
				'base' => '/',
				'here' => '/',
				'webroot' => '/base/',
			])
		);
		Router::parse('/');

		$result = Router::url(['page' => 1, 0 => null, 'sort' => 'controller', 'direction' => 'asc', 'order' => null]);
		$expected = "/admin/controller/index/page:1/sort:controller/direction:asc";
		$this->assertEquals($expected, $result);

		Router::reload();
		$request = new CakeRequest('admin/controller/index');
		$request->addParams([
			'admin' => true, 'controller' => 'controller', 'action' => 'index', 'plugin' => null
		]);
		$request->base = '/';
		Router::setRequestInfo($request);

		Router::parse('/admin/controller/index/type:whatever');
		$result = Router::url(['type' => 'new']);
		$expected = "/admin/controller/index/type:new";
		$this->assertEquals($expected, $result);
	}

/**
 * testNamedArgsUrlParsing method
 *
 * @return void
 */
	public function testNamedArgsUrlParsing() {
		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';
		$result = Router::parse('/controller/action/param1:value1:1/param2:value2:3/param:value');
		$expected = ['pass' => [], 'named' => ['param1' => 'value1:1', 'param2' => 'value2:3', 'param' => 'value'], 'controller' => 'controller', 'action' => 'action', 'plugin' => null];
		$this->assertEquals($expected, $result);

		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';
		$result = Router::connectNamed(false);
		$this->assertEquals([], array_keys($result['rules']));
		$this->assertFalse($result['greedyNamed']);
		$result = Router::parse('/controller/action/param1:value1:1/param2:value2:3/param:value');
		$expected = ['pass' => ['param1:value1:1', 'param2:value2:3', 'param:value'], 'named' => [], 'controller' => 'controller', 'action' => 'action', 'plugin' => null];
		$this->assertEquals($expected, $result);

		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';
		$result = Router::connectNamed(true);
		$named = Router::namedConfig();
		$this->assertEquals($named['default'], array_keys($result['rules']));
		$this->assertTrue($result['greedyNamed']);

		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';
		Router::connectNamed(['param1' => 'not-matching']);
		$result = Router::parse('/controller/action/param1:value1:1/param2:value2:3/param:value');
		$expected = ['pass' => ['param1:value1:1'], 'named' => ['param2' => 'value2:3', 'param' => 'value'], 'controller' => 'controller', 'action' => 'action', 'plugin' => null];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/foo/view/param1:value1:1/param2:value2:3/param:value');
		$expected = ['pass' => ['param1:value1:1'], 'named' => ['param2' => 'value2:3', 'param' => 'value'], 'controller' => 'foo', 'action' => 'view', 'plugin' => null];
		$this->assertEquals($expected, $result);

		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';
		Router::connectNamed(['param1' => '[\d]', 'param2' => '[a-z]', 'param3' => '[\d]']);
		$result = Router::parse('/controller/action/param1:1/param2:2/param3:3');
		$expected = ['pass' => ['param2:2'], 'named' => ['param1' => '1', 'param3' => '3'], 'controller' => 'controller', 'action' => 'action', 'plugin' => null];
		$this->assertEquals($expected, $result);

		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';
		Router::connectNamed(['param1' => '[\d]', 'param2' => true, 'param3' => '[\d]']);
		$result = Router::parse('/controller/action/param1:1/param2:2/param3:3');
		$expected = ['pass' => [], 'named' => ['param1' => '1', 'param2' => '2', 'param3' => '3'], 'controller' => 'controller', 'action' => 'action', 'plugin' => null];
		$this->assertEquals($expected, $result);

		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';
		Router::connectNamed(['param1' => 'value[\d]+:[\d]+'], ['greedy' => false]);
		$result = Router::parse('/controller/action/param1:value1:1/param2:value2:3/param3:value');
		$expected = ['pass' => ['param2:value2:3', 'param3:value'], 'named' => ['param1' => 'value1:1'], 'controller' => 'controller', 'action' => 'action', 'plugin' => null];
		$this->assertEquals($expected, $result);
	}

/**
 * Test URL generation with legacy (1.2) style prefix routes.
 *
 * @return void
 * @see testUrlGenerationWithAutoPrefixes
 */
	public function testUrlGenerationWithLegacyPrefixes() {
		Router::reload();
		Router::connect('/protected/:controller/:action/*', [
			'prefix' => 'protected',
			'protected' => true
		]);
		Router::parse('/');

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'images', 'action' => 'index',
				'prefix' => null, 'admin' => false, 'url' => ['url' => 'images/index']
			])->addPaths([
				'base' => '',
				'here' => '/images/index',
				'webroot' => '/',
			])
		);

		$result = Router::url(['protected' => true]);
		$expected = '/protected/images/index';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'images', 'action' => 'add']);
		$expected = '/images/add';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'images', 'action' => 'add', 'protected' => true]);
		$expected = '/protected/images/add';
		$this->assertEquals($expected, $result);

		$result = Router::url(['action' => 'edit', 1]);
		$expected = '/images/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['action' => 'edit', 1, 'protected' => true]);
		$expected = '/protected/images/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['action' => 'protected_edit', 1, 'protected' => true]);
		$expected = '/protected/images/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['action' => 'edit', 1, 'protected' => true]);
		$expected = '/protected/images/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'others', 'action' => 'edit', 1]);
		$expected = '/others/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'others', 'action' => 'edit', 1, 'protected' => true]);
		$expected = '/protected/others/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'others', 'action' => 'edit', 1, 'protected' => true, 'page' => 1]);
		$expected = '/protected/others/edit/1/page:1';
		$this->assertEquals($expected, $result);

		Router::connectNamed(['random']);
		$result = Router::url(['controller' => 'others', 'action' => 'edit', 1, 'protected' => true, 'random' => 'my-value']);
		$expected = '/protected/others/edit/1/random:my-value';
		$this->assertEquals($expected, $result);
	}

/**
 * test newer style automatically generated prefix routes.
 *
 * @return void
 */
	public function testUrlGenerationWithAutoPrefixes() {
		Configure::write('Routing.prefixes', ['protected']);
		Router::reload();
		Router::parse('/');

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'images', 'action' => 'index',
				'prefix' => null, 'protected' => false, 'url' => ['url' => 'images/index']
			])->addPaths([
				'base' => '',
				'here' => '/images/index',
				'webroot' => '/',
			])
		);

		$result = Router::url(['controller' => 'images', 'action' => 'add']);
		$expected = '/images/add';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'images', 'action' => 'add', 'protected' => true]);
		$expected = '/protected/images/add';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'images', 'action' => 'add_protected_test', 'protected' => true]);
		$expected = '/protected/images/add_protected_test';
		$this->assertEquals($expected, $result);

		$result = Router::url(['action' => 'edit', 1]);
		$expected = '/images/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['action' => 'edit', 1, 'protected' => true]);
		$expected = '/protected/images/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['action' => 'protected_edit', 1, 'protected' => true]);
		$expected = '/protected/images/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['action' => 'protectededit', 1, 'protected' => true]);
		$expected = '/protected/images/protectededit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['action' => 'edit', 1, 'protected' => true]);
		$expected = '/protected/images/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'others', 'action' => 'edit', 1]);
		$expected = '/others/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'others', 'action' => 'edit', 1, 'protected' => true]);
		$expected = '/protected/others/edit/1';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'others', 'action' => 'edit', 1, 'protected' => true, 'page' => 1]);
		$expected = '/protected/others/edit/1/page:1';
		$this->assertEquals($expected, $result);

		Router::connectNamed(['random']);
		$result = Router::url(['controller' => 'others', 'action' => 'edit', 1, 'protected' => true, 'random' => 'my-value']);
		$expected = '/protected/others/edit/1/random:my-value';
		$this->assertEquals($expected, $result);
	}

/**
 * test that auto-generated prefix routes persist
 *
 * @return void
 */
	public function testAutoPrefixRoutePersistence() {
		Configure::write('Routing.prefixes', ['protected']);
		Router::reload();
		Router::parse('/');

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'images', 'action' => 'index', 'prefix' => 'protected',
				'protected' => true, 'url' => ['url' => 'protected/images/index']
			])->addPaths([
				'base' => '',
				'here' => '/protected/images/index',
				'webroot' => '/',
			])
		);

		$result = Router::url(['controller' => 'images', 'action' => 'add']);
		$expected = '/protected/images/add';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'images', 'action' => 'add', 'protected' => false]);
		$expected = '/images/add';
		$this->assertEquals($expected, $result);
	}

/**
 * test that setting a prefix override the current one
 *
 * @return void
 */
	public function testPrefixOverride() {
		Configure::write('Routing.prefixes', ['protected', 'admin']);
		Router::reload();
		Router::parse('/');

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'images', 'action' => 'index', 'prefix' => 'protected',
				'protected' => true, 'url' => ['url' => 'protected/images/index']
			])->addPaths([
				'base' => '',
				'here' => '/protected/images/index',
				'webroot' => '/',
			])
		);

		$result = Router::url(['controller' => 'images', 'action' => 'add', 'admin' => true]);
		$expected = '/admin/images/add';
		$this->assertEquals($expected, $result);

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'images', 'action' => 'index', 'prefix' => 'admin',
				'admin' => true, 'url' => ['url' => 'admin/images/index']
			])->addPaths([
				'base' => '',
				'here' => '/admin/images/index',
				'webroot' => '/',
			])
		);
		$result = Router::url(['controller' => 'images', 'action' => 'add', 'protected' => true]);
		$expected = '/protected/images/add';
		$this->assertEquals($expected, $result);
	}

/**
 * Test that setting a prefix to false is ignored, as its generally user error.
 *
 * @return void
 */
	public function testPrefixFalseIgnored() {
		Configure::write('Routing.prefixes', ['admin']);
		Router::reload();

		Router::connect('/cache_css/*', ['admin' => false, 'controller' => 'asset_compress', 'action' => 'get']);

		$url = Router::url(['controller' => 'asset_compress', 'action' => 'get', 'test']);
		$expected = '/cache_css/test';
		$this->assertEquals($expected, $url);

		$url = Router::url(['admin' => false, 'controller' => 'asset_compress', 'action' => 'get', 'test']);
		$expected = '/cache_css/test';
		$this->assertEquals($expected, $url);

		$url = Router::url(['admin' => true, 'controller' => 'asset_compress', 'action' => 'get', 'test']);
		$this->assertEquals('/admin/asset_compress/get/test', $url);
	}

/**
 * testRemoveBase method
 *
 * @return void
 */
	public function testRemoveBase() {
		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'controller', 'action' => 'index',
				'bare' => 0, 'url' => ['url' => 'protected/images/index']
			])->addPaths([
				'base' => '/base',
				'here' => '/',
				'webroot' => '/base/',
			])
		);

		$result = Router::url(['controller' => 'my_controller', 'action' => 'my_action']);
		$expected = '/base/my_controller/my_action';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'my_controller', 'action' => 'my_action', 'base' => false]);
		$expected = '/my_controller/my_action';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'my_controller', 'action' => 'my_action', 'base' => true]);
		$expected = '/base/my_controller/my_action/base:1';
		$this->assertEquals($expected, $result);
	}

/**
 * testPagesUrlParsing method
 *
 * @return void
 */
	public function testPagesUrlParsing() {
		Router::connect('/', ['controller' => 'pages', 'action' => 'display', 'home']);
		Router::connect('/pages/*', ['controller' => 'pages', 'action' => 'display']);

		$result = Router::parse('/');
		$expected = ['pass' => ['home'], 'named' => [], 'plugin' => null, 'controller' => 'pages', 'action' => 'display'];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/pages/home/');
		$expected = ['pass' => ['home'], 'named' => [], 'plugin' => null, 'controller' => 'pages', 'action' => 'display'];
		$this->assertEquals($expected, $result);

		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';
		Router::connect('/', ['controller' => 'pages', 'action' => 'display', 'home']);

		$result = Router::parse('/');
		$expected = ['pass' => ['home'], 'named' => [], 'plugin' => null, 'controller' => 'pages', 'action' => 'display'];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/pages/display/home/event:value');
		$expected = ['pass' => ['home'], 'named' => ['event' => 'value'], 'plugin' => null, 'controller' => 'pages', 'action' => 'display'];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/pages/display/home/event:Val_u2');
		$expected = ['pass' => ['home'], 'named' => ['event' => 'Val_u2'], 'plugin' => null, 'controller' => 'pages', 'action' => 'display'];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/pages/display/home/event:val-ue');
		$expected = ['pass' => ['home'], 'named' => ['event' => 'val-ue'], 'plugin' => null, 'controller' => 'pages', 'action' => 'display'];
		$this->assertEquals($expected, $result);

		Router::reload();
		Router::connect('/', ['controller' => 'posts', 'action' => 'index']);
		Router::connect('/pages/*', ['controller' => 'pages', 'action' => 'display']);
		$result = Router::parse('/pages/contact/');

		$expected = ['pass' => ['contact'], 'named' => [], 'plugin' => null, 'controller' => 'pages', 'action' => 'display'];
		$this->assertEquals($expected, $result);
	}

/**
 * test that requests with a trailing dot don't loose the do.
 *
 * @return void
 */
	public function testParsingWithTrailingPeriod() {
		Router::reload();
		Router::connect('/:controller/:action/*');
		$result = Router::parse('/posts/view/something.');
		$this->assertEquals('something.', $result['pass'][0], 'Period was chopped off %s');

		$result = Router::parse('/posts/view/something. . .');
		$this->assertEquals('something. . .', $result['pass'][0], 'Period was chopped off %s');
	}

/**
 * test that requests with a trailing dot don't loose the do.
 *
 * @return void
 */
	public function testParsingWithTrailingPeriodAndParseExtensions() {
		Router::reload();
		Router::connect('/:controller/:action/*');
		Router::parseExtensions('json');

		$result = Router::parse('/posts/view/something.');
		$this->assertEquals('something.', $result['pass'][0], 'Period was chopped off %s');

		$result = Router::parse('/posts/view/something. . .');
		$this->assertEquals('something. . .', $result['pass'][0], 'Period was chopped off %s');
	}

/**
 * test that patterns work for :action
 *
 * @return void
 */
	public function testParsingWithPatternOnAction() {
		Router::reload();
		Router::connect(
			'/blog/:action/*',
			['controller' => 'blog_posts'],
			['action' => 'other|actions']
		);
		$result = Router::parse('/blog/other');
		$expected = [
			'plugin' => null,
			'controller' => 'blog_posts',
			'action' => 'other',
			'pass' => [],
			'named' => []
		];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/blog/foobar');
		$this->assertSame([], $result);

		$result = Router::url(['controller' => 'blog_posts', 'action' => 'foo']);
		$this->assertEquals('/blog_posts/foo', $result);

		$result = Router::url(['controller' => 'blog_posts', 'action' => 'actions']);
		$this->assertEquals('/blog/actions', $result);
	}

/**
 * testParsingWithPrefixes method
 *
 * @return void
 */
	public function testParsingWithPrefixes() {
		$adminParams = ['prefix' => 'admin', 'admin' => true];
		Router::connect('/admin/:controller', $adminParams);
		Router::connect('/admin/:controller/:action', $adminParams);
		Router::connect('/admin/:controller/:action/*', $adminParams);

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'controller', 'action' => 'index'
			])->addPaths([
				'base' => '/base',
				'here' => '/',
				'webroot' => '/base/',
			])
		);

		$result = Router::parse('/admin/posts/');
		$expected = ['pass' => [], 'named' => [], 'prefix' => 'admin', 'plugin' => null, 'controller' => 'posts', 'action' => 'admin_index', 'admin' => true];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/admin/posts');
		$this->assertEquals($expected, $result);

		$result = Router::url(['admin' => true, 'controller' => 'posts']);
		$expected = '/base/admin/posts';
		$this->assertEquals($expected, $result);

		$result = Router::prefixes();
		$expected = ['admin'];
		$this->assertEquals($expected, $result);

		Router::reload();

		$prefixParams = ['prefix' => 'members', 'members' => true];
		Router::connect('/members/:controller', $prefixParams);
		Router::connect('/members/:controller/:action', $prefixParams);
		Router::connect('/members/:controller/:action/*', $prefixParams);

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'controller', 'action' => 'index',
				'bare' => 0
			])->addPaths([
				'base' => '/base',
				'here' => '/',
				'webroot' => '/',
			])
		);

		$result = Router::parse('/members/posts/index');
		$expected = ['pass' => [], 'named' => [], 'prefix' => 'members', 'plugin' => null, 'controller' => 'posts', 'action' => 'members_index', 'members' => true];
		$this->assertEquals($expected, $result);

		$result = Router::url(['members' => true, 'controller' => 'posts', 'action' => 'index', 'page' => 2]);
		$expected = '/base/members/posts/index/page:2';
		$this->assertEquals($expected, $result);

		$result = Router::url(['members' => true, 'controller' => 'users', 'action' => 'add']);
		$expected = '/base/members/users/add';
		$this->assertEquals($expected, $result);
	}

/**
 * Tests URL generation with flags and prefixes in and out of context
 *
 * @return void
 */
	public function testUrlWritingWithPrefixes() {
		Router::connect('/company/:controller/:action/*', ['prefix' => 'company', 'company' => true]);
		Router::connect('/login', ['controller' => 'users', 'action' => 'login']);

		$result = Router::url(['controller' => 'users', 'action' => 'login', 'company' => true]);
		$expected = '/company/users/login';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'users', 'action' => 'company_login', 'company' => true]);
		$expected = '/company/users/login';
		$this->assertEquals($expected, $result);

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'users', 'action' => 'login',
				'company' => true
			])->addPaths([
				'base' => '/',
				'here' => '/',
				'webroot' => '/base/',
			])
		);

		$result = Router::url(['controller' => 'users', 'action' => 'login', 'company' => false]);
		$expected = '/login';
		$this->assertEquals($expected, $result);
	}

/**
 * test url generation with prefixes and custom routes
 *
 * @return void
 */
	public function testUrlWritingWithPrefixesAndCustomRoutes() {
		Router::connect(
			'/admin/login',
			['controller' => 'users', 'action' => 'login', 'prefix' => 'admin', 'admin' => true]
		);
		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'posts', 'action' => 'index',
				'admin' => true, 'prefix' => 'admin'
			])->addPaths([
				'base' => '/',
				'here' => '/',
				'webroot' => '/',
			])
		);
		$result = Router::url(['controller' => 'users', 'action' => 'login', 'admin' => true]);
		$this->assertEquals('/admin/login', $result);

		$result = Router::url(['controller' => 'users', 'action' => 'login']);
		$this->assertEquals('/admin/login', $result);

		$result = Router::url(['controller' => 'users', 'action' => 'admin_login']);
		$this->assertEquals('/admin/login', $result);
	}

/**
 * testPassedArgsOrder method
 *
 * @return void
 */
	public function testPassedArgsOrder() {
		Router::connect('/test-passed/*', ['controller' => 'pages', 'action' => 'display', 'home']);
		Router::connect('/test2/*', ['controller' => 'pages', 'action' => 'display', 2]);
		Router::connect('/test/*', ['controller' => 'pages', 'action' => 'display', 1]);
		Router::parse('/');

		$result = Router::url(['controller' => 'pages', 'action' => 'display', 1, 'whatever']);
		$expected = '/test/whatever';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'pages', 'action' => 'display', 2, 'whatever']);
		$expected = '/test2/whatever';
		$this->assertEquals($expected, $result);

		$result = Router::url(['controller' => 'pages', 'action' => 'display', 'home', 'whatever']);
		$expected = '/test-passed/whatever';
		$this->assertEquals($expected, $result);

		Configure::write('Routing.prefixes', ['admin']);
		Router::reload();

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'images', 'action' => 'index',
				'url' => ['url' => 'protected/images/index']
			])->addPaths([
				'base' => '',
				'here' => '/protected/images/index',
				'webroot' => '/',
			])
		);

		Router::connect('/protected/:controller/:action/*', [
			'controller' => 'users',
			'action' => 'index',
			'prefix' => 'protected'
		]);

		Router::parse('/');
		$result = Router::url(['controller' => 'images', 'action' => 'add']);
		$expected = '/protected/images/add';
		$this->assertEquals($expected, $result);

		$result = Router::prefixes();
		$expected = ['admin', 'protected'];
		$this->assertEquals($expected, $result);
	}

/**
 * testRegexRouteMatching method
 *
 * @return void
 */
	public function testRegexRouteMatching() {
		Router::connect('/:locale/:controller/:action/*', [], ['locale' => 'dan|eng']);

		$result = Router::parse('/eng/test/test_action');
		$expected = ['pass' => [], 'named' => [], 'locale' => 'eng', 'controller' => 'test', 'action' => 'test_action', 'plugin' => null];
		$this->assertEquals($expected, $result);

		$result = Router::parse('/badness/test/test_action');
		$this->assertSame([], $result);

		Router::reload();
		Router::connect('/:locale/:controller/:action/*', [], ['locale' => 'dan|eng']);

		$request = new CakeRequest();
		Router::setRequestInfo(
			$request->addParams([
				'plugin' => null, 'controller' => 'test', 'action' => 'index',
				'url' => ['url' => 'test/test_action']
			])->addPaths([
				'base' => '',
				'here' => '/test/test_action',
				'webroot' => '/',
			])
		);

		$result = Router::url(['action' => 'test_another_action']);
		$expected = '/test/test_another_action';
		$this->assertEquals($expected, $result);

		$result = Router::url(['action' => 'test_another_action', 'locale' => 'eng']);
		$expected = '/eng/test/test_another_action';
		$this->assertEquals($expected, $result);

		$result = Router::url(['action' => 'test_another_action', 'locale' => 'badness']);
		$expected = '/test/test_another_action/locale:badness';
		$this->assertEquals($expected, $result);
	}

/**
 * testStripPlugin
 *
 * @return void
 */
	public function testStripPlugin() {
		$pluginName = 'forums';
		$url = 'example.com/' . $pluginName . '/';
		$expected = 'example.com';

		$this->assertEquals($expected, Router::stripPlugin($url, $pluginName));
		$this->assertEquals(Router::stripPlugin($url), $url);
		$this->assertEquals(Router::stripPlugin($url, null), $url);
	}

/**
 * testCurrentRouteWhenNonExistentRoute
 *
 * @return void
 */
	public function testCurrentRouteWhenNonExistentRoute() {
		$route = Router::currentRoute();
		$this->assertFalse($route);
	}

/**
 * testCurrentRoute
 *
 * This test needs some improvement and actual requestAction() usage
 *
 * @return void
 */
	public function testCurrentRoute() {
		$url = ['controller' => 'pages', 'action' => 'display', 'government'];
		Router::connect('/government', $url);
		Router::parse('/government');
		$route = Router::currentRoute();
		$this->assertEquals(array_merge($url, ['plugin' => null]), $route->defaults);
	}

/**
 * testRequestRoute
 *
 * @return void
 */
	public function testRequestRoute() {
		$url = ['controller' => 'products', 'action' => 'display', 5];
		Router::connect('/government', $url);
		Router::parse('/government');
		$route = Router::requestRoute();
		$this->assertEquals(array_merge($url, ['plugin' => null]), $route->defaults);

		// test that the first route is matched
		Router::connect('/government', $url);
		Router::parse('/government');
		$route = Router::requestRoute();
		$this->assertEquals(array_merge($url, ['plugin' => null]), $route->defaults);

		// test that an unmatched route does not change the current route
		Router::connect('/actor', $url);
		Router::parse('/government');
		$route = Router::requestRoute();
		$this->assertEquals(array_merge($url, ['plugin' => null]), $route->defaults);
	}

/**
 * testGetParams
 *
 * @return void
 */
	public function testGetParams() {
		$paths = ['base' => '/', 'here' => '/products/display/5', 'webroot' => '/webroot'];
		$params = ['param1' => '1', 'param2' => '2'];
		Router::setRequestInfo([$params, $paths]);

		$expected = [
			'plugin' => null, 'controller' => false, 'action' => false,
			'named' => [], 'pass' => [],
			'param1' => '1', 'param2' => '2',
		];
		$this->assertEquals($expected, Router::getParams());
		$this->assertEquals(false, Router::getParam('controller'));
		$this->assertEquals('1', Router::getParam('param1'));
		$this->assertEquals('2', Router::getParam('param2'));

		Router::reload();

		$params = ['controller' => 'pages', 'action' => 'display'];
		Router::setRequestInfo([$params, $paths]);
		$expected = [
			'plugin' => null, 'controller' => 'pages', 'action' => 'display',
			'named' => [], 'pass' => [],
		];
		$this->assertEquals($expected, Router::getParams());
		$this->assertEquals($expected, Router::getParams(true));
	}

/**
 * test that connectDefaults() can disable default route connection
 *
 * @return void
 */
	public function testDefaultsMethod() {
		Router::connect('/test/*', ['controller' => 'pages', 'action' => 'display', 2]);
		$result = Router::parse('/posts/edit/5');
		$this->assertFalse(isset($result['controller']));
		$this->assertFalse(isset($result['action']));
	}

/**
 * test that the required default routes are connected.
 *
 * @return void
 */
	public function testConnectDefaultRoutes() {
		App::build([
			'plugins' => [
				CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS
			]
		], App::RESET);
		CakePlugin::load(['TestPlugin', 'PluginJs']);
		Router::reload();
		require CAKE . 'Config' . DS . 'routes.php';

		$result = Router::url(['plugin' => 'plugin_js', 'controller' => 'js_file', 'action' => 'index']);
		$this->assertEquals('/plugin_js/js_file', $result);

		$result = Router::parse('/plugin_js/js_file');
		$expected = [
			'plugin' => 'plugin_js', 'controller' => 'js_file', 'action' => 'index',
			'named' => [], 'pass' => []
		];
		$this->assertEquals($expected, $result);

		$result = Router::url(['plugin' => 'test_plugin', 'controller' => 'test_plugin', 'action' => 'index']);
		$this->assertEquals('/test_plugin', $result);

		$result = Router::parse('/test_plugin');
		$expected = [
			'plugin' => 'test_plugin', 'controller' => 'test_plugin', 'action' => 'index',
			'named' => [], 'pass' => []
		];

		$this->assertEquals($expected, $result, 'Plugin shortcut route broken. %s');
	}

/**
 * test using a custom route class for route connection
 *
 * @return void
 */
	public function testUsingCustomRouteClass() {
		$this->getMock('CakeRoute', [], [], 'MockConnectedRoute', false);
		$routes = Router::connect(
			'/:slug',
			['controller' => 'posts', 'action' => 'view'],
			['routeClass' => 'MockConnectedRoute', 'slug' => '[a-z_-]+']
		);
		$this->assertInstanceOf('MockConnectedRoute', $routes[0], 'Incorrect class used. %s');
		$expected = ['controller' => 'posts', 'action' => 'view', 'slug' => 'test'];
		$routes[0]->expects($this->any())
			->method('parse')
			->will($this->returnValue($expected));
		$result = Router::parse('/test');
		$this->assertEquals($expected, $result);
	}

/**
 * test using custom route class in PluginDot notation
 *
 * @return void
 */
	public function testUsingCustomRouteClassPluginDotSyntax() {
		App::build([
			'Plugin' => [
				CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS
			]
		]);
		CakePlugin::load('TestPlugin');
		App::uses('TestRoute', 'TestPlugin.Routing/Route');
		$routes = Router::connect(
			'/:slug',
			['controller' => 'posts', 'action' => 'view'],
			['routeClass' => 'TestPlugin.TestRoute', 'slug' => '[a-z_-]+']
		);
		$this->assertInstanceOf('TestRoute', $routes[0]);
		CakePlugin::unload('TestPlugin');
	}

/**
 * test that route classes must extend CakeRoute
 *
 * @expectedException RouterException
 * @return void
 */
	public function testCustomRouteException() {
		Router::connect('/:controller', [], ['routeClass' => 'Object']);
	}

/**
 * test reversing parameter arrays back into strings.
 *
 * Mark the router as initialized so it doesn't auto-load routes
 *
 * @return void
 */
	public function testRouterReverse() {
		Router::$initialized = true;

		$params = [
			'controller' => 'posts',
			'action' => 'view',
			'pass' => [1],
			'named' => [],
			'url' => [],
			'autoRender' => 1,
			'bare' => 1,
			'return' => 1,
			'requested' => 1,
			'_Token' => ['key' => 'sekret']
		];
		$result = Router::reverse($params);
		$this->assertEquals('/posts/view/1', $result);

		$params = [
			'controller' => 'posts',
			'action' => 'index',
			'pass' => [1],
			'named' => ['page' => 1, 'sort' => 'Article.title', 'direction' => 'desc'],
			'url' => []
		];
		$result = Router::reverse($params);
		$this->assertEquals('/posts/index/1/page:1/sort:Article.title/direction:desc', $result);

		Router::connect('/:lang/:controller/:action/*', [], ['lang' => '[a-z]{3}']);
		$params = [
			'lang' => 'eng',
			'controller' => 'posts',
			'action' => 'view',
			'pass' => [1],
			'named' => [],
			'url' => ['url' => 'eng/posts/view/1']
		];
		$result = Router::reverse($params);
		$this->assertEquals('/eng/posts/view/1', $result);

		$params = [
			'lang' => 'eng',
			'controller' => 'posts',
			'action' => 'view',
			'pass' => [1],
			'named' => [],
			'url' => ['url' => 'eng/posts/view/1', 'foo' => 'bar', 'baz' => 'quu'],
			'paging' => [],
			'models' => []
		];
		$result = Router::reverse($params);
		$this->assertEquals('/eng/posts/view/1?foo=bar&baz=quu', $result);

		$request = new CakeRequest('/eng/posts/view/1');
		$request->addParams([
			'lang' => 'eng',
			'controller' => 'posts',
			'action' => 'view',
			'pass' => [1],
			'named' => [],
		]);
		$request->query = ['url' => 'eng/posts/view/1', 'test' => 'value'];
		$result = Router::reverse($request);
		$expected = '/eng/posts/view/1?test=value';
		$this->assertEquals($expected, $result);

		$params = [
			'lang' => 'eng',
			'controller' => 'posts',
			'action' => 'view',
			'pass' => [1],
			'named' => [],
			'url' => ['url' => 'eng/posts/view/1']
		];
		$result = Router::reverse($params, true);
		$this->assertRegExp('/^http(s)?:\/\//', $result);
	}

/**
 * Test that extensions work with Router::reverse()
 *
 * @return void
 */
	public function testReverseWithExtension() {
		Router::parseExtensions('json');

		$request = new CakeRequest('/posts/view/1.json');
		$request->addParams([
			'controller' => 'posts',
			'action' => 'view',
			'pass' => [1],
			'named' => [],
			'ext' => 'json',
		]);
		$request->query = [];
		$result = Router::reverse($request);
		$expected = '/posts/view/1.json';
		$this->assertEquals($expected, $result);
	}

/**
 * test that setRequestInfo can accept arrays and turn that into a CakeRequest object.
 *
 * @return void
 */
	public function testSetRequestInfoLegacy() {
		Router::setRequestInfo([
			[
				'plugin' => null, 'controller' => 'images', 'action' => 'index',
				'url' => ['url' => 'protected/images/index']
			],
			[
				'base' => '',
				'here' => '/protected/images/index',
				'webroot' => '/',
			]
		]);
		$result = Router::getRequest();
		$this->assertEquals('images', $result->controller);
		$this->assertEquals('index', $result->action);
		$this->assertEquals('', $result->base);
		$this->assertEquals('/protected/images/index', $result->here);
		$this->assertEquals('/', $result->webroot);
	}

/**
 * Test that Router::url() uses the first request
 *
 * @return void
 */
	public function testUrlWithRequestAction() {
		$firstRequest = new CakeRequest('/posts/index');
		$firstRequest->addParams([
			'plugin' => null,
			'controller' => 'posts',
			'action' => 'index'
		])->addPaths(['base' => '']);

		$secondRequest = new CakeRequest('/posts/index');
		$secondRequest->addParams([
			'requested' => 1,
			'plugin' => null,
			'controller' => 'comments',
			'action' => 'listing'
		])->addPaths(['base' => '']);

		Router::setRequestInfo($firstRequest);
		Router::setRequestInfo($secondRequest);

		$result = Router::url(['base' => false]);
		$this->assertEquals('/comments/listing', $result, 'with second requests, the last should win.');

		Router::popRequest();
		$result = Router::url(['base' => false]);
		$this->assertEquals('/posts', $result, 'with second requests, the last should win.');
	}

/**
 * test that a route object returning a full URL is not modified.
 *
 * @return void
 */
	public function testUrlFullUrlReturnFromRoute() {
		$url = 'http://example.com/posts/view/1';

		$this->getMock('CakeRoute', [], ['/'], 'MockReturnRoute');
		$routes = Router::connect('/:controller/:action', [], ['routeClass' => 'MockReturnRoute']);
		$routes[0]->expects($this->any())->method('match')
			->will($this->returnValue($url));

		$result = Router::url(['controller' => 'posts', 'action' => 'view', 1]);
		$this->assertEquals($url, $result);
	}

/**
 * test protocol in url
 *
 * @return void
 */
	public function testUrlProtocol() {
		$url = 'http://example.com';
		$this->assertEquals($url, Router::url($url));

		$url = 'ed2k://example.com';
		$this->assertEquals($url, Router::url($url));

		$url = 'svn+ssh://example.com';
		$this->assertEquals($url, Router::url($url));

		$url = '://example.com';
		$this->assertEquals($url, Router::url($url));

		$url = '//example.com';
		$this->assertEquals($url, Router::url($url));

		$url = 'javascript:void(0)';
		$this->assertEquals($url, Router::url($url));

		$url = 'tel:012345-678';
		$this->assertEquals($url, Router::url($url));

		$url = 'sms:012345-678';
		$this->assertEquals($url, Router::url($url));

		$url = '#here';
		$this->assertEquals($url, Router::url($url));

		$url = '?param=0';
		$this->assertEquals($url, Router::url($url));

		$url = 'posts/index#here';
		$expected = FULL_BASE_URL . '/posts/index#here';
		$this->assertEquals($expected, Router::url($url, true));
	}

/**
 * Testing that patterns on the :action param work properly.
 *
 * @return void
 */
	public function testPatternOnAction() {
		$route = new CakeRoute(
			'/blog/:action/*',
			['controller' => 'blog_posts'],
			['action' => 'other|actions']
		);
		$result = $route->match(['controller' => 'blog_posts', 'action' => 'foo']);
		$this->assertFalse($result);

		$result = $route->match(['controller' => 'blog_posts', 'action' => 'actions']);
		$this->assertEquals('/blog/actions/', $result);

		$result = $route->parse('/blog/other');
		$expected = ['controller' => 'blog_posts', 'action' => 'other', 'pass' => [], 'named' => []];
		$this->assertEquals($expected, $result);

		$result = $route->parse('/blog/foobar');
		$this->assertFalse($result);
	}

/**
 * Tests resourceMap as getter and setter.
 *
 * @return void
 */
	public function testResourceMap() {
		$default = Router::resourceMap();
		$expected = [
			['action' => 'index', 'method' => 'GET', 'id' => false],
			['action' => 'view', 'method' => 'GET', 'id' => true],
			['action' => 'add', 'method' => 'POST', 'id' => false],
			['action' => 'edit', 'method' => 'PUT', 'id' => true],
			['action' => 'delete', 'method' => 'DELETE', 'id' => true],
			['action' => 'edit', 'method' => 'POST', 'id' => true]
		];
		$this->assertEquals($expected, $default);

		$custom = [
			['action' => 'index', 'method' => 'GET', 'id' => false],
			['action' => 'view', 'method' => 'GET', 'id' => true],
			['action' => 'add', 'method' => 'POST', 'id' => false],
			['action' => 'edit', 'method' => 'PUT', 'id' => true],
			['action' => 'delete', 'method' => 'DELETE', 'id' => true],
			['action' => 'update', 'method' => 'POST', 'id' => true]
		];
		Router::resourceMap($custom);
		$this->assertEquals(Router::resourceMap(), $custom);

		Router::resourceMap($default);
	}

/**
 * test setting redirect routes
 *
 * @return void
 */
	public function testRouteRedirection() {
		Router::redirect('/blog', ['controller' => 'posts'], ['status' => 302]);
		$this->assertEquals(1, count(Router::$routes));
		Router::$routes[0]->response = $this->getMock('CakeResponse', ['_sendHeader']);
		Router::$routes[0]->stop = false;
		$this->assertEquals(302, Router::$routes[0]->options['status']);

		Router::parse('/blog');
		$header = Router::$routes[0]->response->header();
		$this->assertEquals(Router::url('/posts', true), $header['Location']);
		$this->assertEquals(302, Router::$routes[0]->response->statusCode());

		Router::$routes[0]->response = $this->getMock('CakeResponse', ['_sendHeader']);
		Router::parse('/not-a-match');
		$this->assertEquals([], Router::$routes[0]->response->header());
	}

/**
 * Test setting the default route class
 *
 * @return void
 */
	public function testDefaultRouteClass() {
		$this->getMock('CakeRoute', [], ['/test'], 'TestDefaultRouteClass');
		Router::defaultRouteClass('TestDefaultRouteClass');

		$result = Router::connect('/', ['controller' => 'pages', 'action' => 'display', 'home']);
		$this->assertInstanceOf('TestDefaultRouteClass', $result[0]);
	}

/**
 * Test getting the default route class
 *
 * @return void
 */
	public function testDefaultRouteClassGetter() {
		$routeClass = 'TestDefaultRouteClass';
		Router::defaultRouteClass($routeClass);

		$this->assertEquals($routeClass, Router::defaultRouteClass());
		$this->assertEquals($routeClass, Router::defaultRouteClass(null));
	}

/**
 * Test that route classes must extend CakeRoute
 *
 * @expectedException RouterException
 * @return void
 */
	public function testDefaultRouteException() {
		Router::defaultRouteClass('');
		Router::connect('/:controller', []);
	}

/**
 * Test that route classes must extend CakeRoute
 *
 * @expectedException RouterException
 * @return void
 */
	public function testSettingInvalidDefaultRouteException() {
		Router::defaultRouteClass('Object');
	}

/**
 * Test that class must exist
 *
 * @expectedException RouterException
 * @return void
 */
	public function testSettingNonExistentDefaultRouteException() {
		Router::defaultRouteClass('NonExistentClass');
	}

/**
 * Tests generating well-formed querystrings
 *
 * @return void
 */
	public function testQueryString() {
		$result = Router::queryString(['var' => 'foo bar']);
		$expected = '?var=foo+bar';
		$this->assertEquals($expected, $result);

		$result = Router::queryString(false, ['some' => 'param', 'foo' => 'bar']);
		$expected = '?some=param&foo=bar';
		$this->assertEquals($expected, $result);

		$existing = ['apple' => 'red', 'pear' => 'green'];
		$result = Router::queryString($existing, ['some' => 'param', 'foo' => 'bar']);
		$expected = '?apple=red&pear=green&some=param&foo=bar';
		$this->assertEquals($expected, $result);

		$existing = 'apple=red&pear=green';
		$result = Router::queryString($existing, ['some' => 'param', 'foo' => 'bar']);
		$expected = '?apple=red&pear=green&some=param&foo=bar';
		$this->assertEquals($expected, $result);

		$existing = '?apple=red&pear=green';
		$result = Router::queryString($existing, ['some' => 'param', 'foo' => 'bar']);
		$expected = '?apple=red&pear=green&some=param&foo=bar';
		$this->assertEquals($expected, $result);

		$result = Router::queryString('apple=red&pear=green');
		$expected = '?apple=red&pear=green';
		$this->assertEquals($expected, $result);

		$result = Router::queryString('foo=bar', ['php' => 'nut', 'jose' => 'zap'], true);
		$expected = '?foo=bar&amp;php=nut&amp;jose=zap';
		$this->assertEquals($expected, $result);

		$result = Router::queryString('foo=bar&amp;', ['php' => 'nut', 'jose' => 'zap'], true);
		$expected = '?foo=bar&amp;php=nut&amp;jose=zap';
		$this->assertEquals($expected, $result);

		$result = Router::queryString('foo=bar&', ['php' => 'nut', 'jose' => 'zap']);
		$expected = '?foo=bar&php=nut&jose=zap';
		$this->assertEquals($expected, $result);
	}
}
