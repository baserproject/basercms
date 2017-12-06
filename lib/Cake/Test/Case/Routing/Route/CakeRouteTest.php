<?php
/**
 * CakeRequest Test case file.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Test.Case.Routing.Route
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('CakeRoute', 'Routing/Route');
App::uses('Router', 'Routing');

/**
 * Test case for CakeRoute
 *
 * @package       Cake.Test.Case.Routing.Route
 */
class CakeRouteTest extends CakeTestCase {

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
 * Test the construction of a CakeRoute
 *
 * @return void
 */
	public function testConstruction() {
		$route = new CakeRoute('/:controller/:action/:id', [], ['id' => '[0-9]+']);

		$this->assertEquals('/:controller/:action/:id', $route->template);
		$this->assertEquals([], $route->defaults);
		$this->assertEquals(['id' => '[0-9]+'], $route->options);
		$this->assertFalse($route->compiled());
	}

/**
 * test Route compiling.
 *
 * @return void
 */
	public function testBasicRouteCompiling() {
		$route = new CakeRoute('/', ['controller' => 'pages', 'action' => 'display', 'home']);
		$result = $route->compile();
		$expected = '#^/*$#';
		$this->assertEquals($expected, $result);
		$this->assertEquals([], $route->keys);

		$route = new CakeRoute('/:controller/:action', ['controller' => 'posts']);
		$result = $route->compile();

		$this->assertRegExp($result, '/posts/edit');
		$this->assertRegExp($result, '/posts/super_delete');
		$this->assertNotRegExp($result, '/posts');
		$this->assertNotRegExp($result, '/posts/super_delete/1');

		$route = new CakeRoute('/posts/foo:id', ['controller' => 'posts', 'action' => 'view']);
		$result = $route->compile();

		$this->assertRegExp($result, '/posts/foo:1');
		$this->assertRegExp($result, '/posts/foo:param');
		$this->assertNotRegExp($result, '/posts');
		$this->assertNotRegExp($result, '/posts/');

		$this->assertEquals(['id'], $route->keys);

		$route = new CakeRoute('/:plugin/:controller/:action/*', ['plugin' => 'test_plugin', 'action' => 'index']);
		$result = $route->compile();
		$this->assertRegExp($result, '/test_plugin/posts/index');
		$this->assertRegExp($result, '/test_plugin/posts/edit/5');
		$this->assertRegExp($result, '/test_plugin/posts/edit/5/name:value/nick:name');
	}

/**
 * test that route parameters that overlap don't cause errors.
 *
 * @return void
 */
	public function testRouteParameterOverlap() {
		$route = new CakeRoute('/invoices/add/:idd/:id', ['controller' => 'invoices', 'action' => 'add']);
		$result = $route->compile();
		$this->assertRegExp($result, '/invoices/add/1/3');

		$route = new CakeRoute('/invoices/add/:id/:idd', ['controller' => 'invoices', 'action' => 'add']);
		$result = $route->compile();
		$this->assertRegExp($result, '/invoices/add/1/3');
	}

/**
 * test compiling routes with keys that have patterns
 *
 * @return void
 */
	public function testRouteCompilingWithParamPatterns() {
		$route = new CakeRoute(
			'/:controller/:action/:id',
			[],
			['id' => Router::ID]
		);
		$result = $route->compile();
		$this->assertRegExp($result, '/posts/edit/1');
		$this->assertRegExp($result, '/posts/view/518098');
		$this->assertNotRegExp($result, '/posts/edit/name-of-post');
		$this->assertNotRegExp($result, '/posts/edit/4/other:param');
		$this->assertEquals(['id', 'controller', 'action'], $route->keys);

		$route = new CakeRoute(
			'/:lang/:controller/:action/:id',
			['controller' => 'testing4'],
			['id' => Router::ID, 'lang' => '[a-z]{3}']
		);
		$result = $route->compile();
		$this->assertRegExp($result, '/eng/posts/edit/1');
		$this->assertRegExp($result, '/cze/articles/view/1');
		$this->assertNotRegExp($result, '/language/articles/view/2');
		$this->assertNotRegExp($result, '/eng/articles/view/name-of-article');
		$this->assertEquals(['lang', 'id', 'controller', 'action'], $route->keys);

		foreach ([':', '@', ';', '$', '-'] as $delim) {
			$route = new CakeRoute('/posts/:id' . $delim . ':title');
			$result = $route->compile();

			$this->assertRegExp($result, '/posts/1' . $delim . 'name-of-article');
			$this->assertRegExp($result, '/posts/13244' . $delim . 'name-of_Article[]');
			$this->assertNotRegExp($result, '/posts/11!nameofarticle');
			$this->assertNotRegExp($result, '/posts/11');

			$this->assertEquals(['title', 'id'], $route->keys);
		}

		$route = new CakeRoute(
			'/posts/:id::title/:year',
			['controller' => 'posts', 'action' => 'view'],
			['id' => Router::ID, 'year' => Router::YEAR, 'title' => '[a-z-_]+']
		);
		$result = $route->compile();
		$this->assertRegExp($result, '/posts/1:name-of-article/2009/');
		$this->assertRegExp($result, '/posts/13244:name-of-article/1999');
		$this->assertNotRegExp($result, '/posts/hey_now:nameofarticle');
		$this->assertNotRegExp($result, '/posts/:nameofarticle/2009');
		$this->assertNotRegExp($result, '/posts/:nameofarticle/01');
		$this->assertEquals(['year', 'title', 'id'], $route->keys);

		$route = new CakeRoute(
			'/posts/:url_title-(uuid::id)',
			['controller' => 'posts', 'action' => 'view'],
			['pass' => ['id', 'url_title'], 'id' => Router::ID]
		);
		$result = $route->compile();
		$this->assertRegExp($result, '/posts/some_title_for_article-(uuid:12534)/');
		$this->assertRegExp($result, '/posts/some_title_for_article-(uuid:12534)');
		$this->assertNotRegExp($result, '/posts/');
		$this->assertNotRegExp($result, '/posts/nameofarticle');
		$this->assertNotRegExp($result, '/posts/nameofarticle-12347');
		$this->assertEquals(['url_title', 'id'], $route->keys);
	}

/**
 * test more complex route compiling & parsing with mid route greedy stars
 * and optional routing parameters
 *
 * @return void
 */
	public function testComplexRouteCompilingAndParsing() {
		$route = new CakeRoute(
			'/posts/:month/:day/:year/*',
			['controller' => 'posts', 'action' => 'view'],
			['year' => Router::YEAR, 'month' => Router::MONTH, 'day' => Router::DAY]
		);
		$result = $route->compile();
		$this->assertRegExp($result, '/posts/08/01/2007/title-of-post');
		$result = $route->parse('/posts/08/01/2007/title-of-post');

		$this->assertEquals(7, count($result));
		$this->assertEquals('posts', $result['controller']);
		$this->assertEquals('view', $result['action']);
		$this->assertEquals('2007', $result['year']);
		$this->assertEquals('08', $result['month']);
		$this->assertEquals('01', $result['day']);
		$this->assertEquals('title-of-post', $result['pass'][0]);

		$route = new CakeRoute(
			"/:extra/page/:slug/*",
			['controller' => 'pages', 'action' => 'view', 'extra' => null],
			["extra" => '[a-z1-9_]*', "slug" => '[a-z1-9_]+', "action" => 'view']
		);
		$result = $route->compile();

		$this->assertRegExp($result, '/some_extra/page/this_is_the_slug');
		$this->assertRegExp($result, '/page/this_is_the_slug');
		$this->assertEquals(['slug', 'extra'], $route->keys);
		$this->assertEquals(['extra' => '[a-z1-9_]*', 'slug' => '[a-z1-9_]+', 'action' => 'view'], $route->options);
		$expected = [
			'controller' => 'pages',
			'action' => 'view'
		];
		$this->assertEquals($expected, $route->defaults);

		$route = new CakeRoute(
			'/:controller/:action/*',
			['project' => false],
			[
				'controller' => 'source|wiki|commits|tickets|comments|view',
				'action' => 'branches|history|branch|logs|view|start|add|edit|modify'
			]
		);
		$this->assertFalse($route->parse('/chaw_test/wiki'));

		$result = $route->compile();
		$this->assertNotRegExp($result, '/some_project/source');
		$this->assertRegExp($result, '/source/view');
		$this->assertRegExp($result, '/source/view/other/params');
		$this->assertNotRegExp($result, '/chaw_test/wiki');
		$this->assertNotRegExp($result, '/source/wierd_action');
	}

/**
 * test that routes match their pattern.
 *
 * @return void
 */
	public function testMatchBasic() {
		$route = new CakeRoute('/:controller/:action/:id', ['plugin' => null]);
		$result = $route->match(['controller' => 'posts', 'action' => 'view', 'plugin' => null]);
		$this->assertFalse($result);

		$result = $route->match(['plugin' => null, 'controller' => 'posts', 'action' => 'view', 0]);
		$this->assertFalse($result);

		$result = $route->match(['plugin' => null, 'controller' => 'posts', 'action' => 'view', 'id' => 1]);
		$this->assertEquals('/posts/view/1', $result);

		$route = new CakeRoute('/', ['controller' => 'pages', 'action' => 'display', 'home']);
		$result = $route->match(['controller' => 'pages', 'action' => 'display', 'home']);
		$this->assertEquals('/', $result);

		$result = $route->match(['controller' => 'pages', 'action' => 'display', 'about']);
		$this->assertFalse($result);

		$route = new CakeRoute('/pages/*', ['controller' => 'pages', 'action' => 'display']);
		$result = $route->match(['controller' => 'pages', 'action' => 'display', 'home']);
		$this->assertEquals('/pages/home', $result);

		$result = $route->match(['controller' => 'pages', 'action' => 'display', 'about']);
		$this->assertEquals('/pages/about', $result);

		$route = new CakeRoute('/blog/:action', ['controller' => 'posts']);
		$result = $route->match(['controller' => 'posts', 'action' => 'view']);
		$this->assertEquals('/blog/view', $result);

		$result = $route->match(['controller' => 'nodes', 'action' => 'view']);
		$this->assertFalse($result);

		$result = $route->match(['controller' => 'posts', 'action' => 'view', 1]);
		$this->assertFalse($result);

		$result = $route->match(['controller' => 'posts', 'action' => 'view', 'id' => 2]);
		$this->assertFalse($result);

		$route = new CakeRoute('/foo/:controller/:action', ['action' => 'index']);
		$result = $route->match(['controller' => 'posts', 'action' => 'view']);
		$this->assertEquals('/foo/posts/view', $result);

		$route = new CakeRoute('/:plugin/:id/*', ['controller' => 'posts', 'action' => 'view']);
		$result = $route->match(['plugin' => 'test', 'controller' => 'posts', 'action' => 'view', 'id' => '1']);
		$this->assertEquals('/test/1/', $result);

		$result = $route->match(['plugin' => 'fo', 'controller' => 'posts', 'action' => 'view', 'id' => '1', '0']);
		$this->assertEquals('/fo/1/0', $result);

		$result = $route->match(['plugin' => 'fo', 'controller' => 'nodes', 'action' => 'view', 'id' => 1]);
		$this->assertFalse($result);

		$result = $route->match(['plugin' => 'fo', 'controller' => 'posts', 'action' => 'edit', 'id' => 1]);
		$this->assertFalse($result);

		$route = new CakeRoute('/admin/subscriptions/:action/*', [
			'controller' => 'subscribe', 'admin' => true, 'prefix' => 'admin'
		]);

		$url = ['controller' => 'subscribe', 'admin' => true, 'action' => 'edit', 1];
		$result = $route->match($url);
		$expected = '/admin/subscriptions/edit/1';
		$this->assertEquals($expected, $result);

		$url = [
			'controller' => 'subscribe',
			'admin' => true,
			'action' => 'edit_admin_e',
			1
		];
		$result = $route->match($url);
		$expected = '/admin/subscriptions/edit_admin_e/1';
		$this->assertEquals($expected, $result);

		$url = ['controller' => 'subscribe', 'admin' => true, 'action' => 'admin_edit', 1];
		$result = $route->match($url);
		$expected = '/admin/subscriptions/edit/1';
		$this->assertEquals($expected, $result);
	}

/**
 * test that non-greedy routes fail with extra passed args
 *
 * @return void
 */
	public function testGreedyRouteFailurePassedArg() {
		$route = new CakeRoute('/:controller/:action', ['plugin' => null]);
		$result = $route->match(['controller' => 'posts', 'action' => 'view', '0']);
		$this->assertFalse($result);

		$route = new CakeRoute('/:controller/:action', ['plugin' => null]);
		$result = $route->match(['controller' => 'posts', 'action' => 'view', 'test']);
		$this->assertFalse($result);
	}

/**
 * test that non-greedy routes fail with extra passed args
 *
 * @return void
 */
	public function testGreedyRouteFailureNamedParam() {
		$route = new CakeRoute('/:controller/:action', ['plugin' => null]);
		$result = $route->match(['controller' => 'posts', 'action' => 'view', 'page' => 1]);
		$this->assertFalse($result);
	}

/**
 * test that falsey values do not interrupt a match.
 *
 * @return void
 */
	public function testMatchWithFalseyValues() {
		$route = new CakeRoute('/:controller/:action/*', ['plugin' => null]);
		$result = $route->match([
			'controller' => 'posts', 'action' => 'index', 'plugin' => null, 'admin' => false
		]);
		$this->assertEquals('/posts/index/', $result);
	}

/**
 * test match() with greedy routes, named parameters and passed args.
 *
 * @return void
 */
	public function testMatchWithNamedParametersAndPassedArgs() {
		Router::connectNamed(true);

		$route = new CakeRoute('/:controller/:action/*', ['plugin' => null]);
		$result = $route->match(['controller' => 'posts', 'action' => 'index', 'plugin' => null, 'page' => 1]);
		$this->assertEquals('/posts/index/page:1', $result);

		$result = $route->match(['controller' => 'posts', 'action' => 'view', 'plugin' => null, 5]);
		$this->assertEquals('/posts/view/5', $result);

		$result = $route->match(['controller' => 'posts', 'action' => 'view', 'plugin' => null, 0]);
		$this->assertEquals('/posts/view/0', $result);

		$result = $route->match(['controller' => 'posts', 'action' => 'view', 'plugin' => null, '0']);
		$this->assertEquals('/posts/view/0', $result);

		$result = $route->match(['controller' => 'posts', 'action' => 'view', 'plugin' => null, 5, 'page' => 1, 'limit' => 20, 'order' => 'title']);
		$this->assertEquals('/posts/view/5/page:1/limit:20/order:title', $result);

		$result = $route->match(['controller' => 'posts', 'action' => 'view', 'plugin' => null, 'word space', 'order' => 'Θ']);
		$this->assertEquals('/posts/view/word%20space/order:%CE%98', $result);

		$route = new CakeRoute('/test2/*', ['controller' => 'pages', 'action' => 'display', 2]);
		$result = $route->match(['controller' => 'pages', 'action' => 'display', 1]);
		$this->assertFalse($result);

		$result = $route->match(['controller' => 'pages', 'action' => 'display', 2, 'something']);
		$this->assertEquals('/test2/something', $result);

		$result = $route->match(['controller' => 'pages', 'action' => 'display', 5, 'something']);
		$this->assertFalse($result);
	}

/**
 * Ensure that named parameters are urldecoded
 *
 * @return void
 */
	public function testParseNamedParametersUrlDecode() {
		Router::connectNamed(true);
		$route = new CakeRoute('/:controller/:action/*', ['plugin' => null]);

		$result = $route->parse('/posts/index/page:%CE%98');
		$this->assertEquals('Θ', $result['named']['page']);

		$result = $route->parse('/posts/index/page[]:%CE%98');
		$this->assertEquals('Θ', $result['named']['page'][0]);

		$result = $route->parse('/posts/index/something%20else/page[]:%CE%98');
		$this->assertEquals('Θ', $result['named']['page'][0]);
		$this->assertEquals('something else', $result['pass'][0]);
	}

/**
 * Ensure that keys at named parameters are urldecoded
 *
 * @return void
 */
	public function testParseNamedKeyUrlDecode() {
		Router::connectNamed(true);
		$route = new CakeRoute('/:controller/:action/*', ['plugin' => null]);

		// checking /post/index/user[0]:a/user[1]:b
		$result = $route->parse('/posts/index/user%5B0%5D:a/user%5B1%5D:b');
		$this->assertArrayHasKey('user', $result['named']);
		$this->assertEquals(['a', 'b'], $result['named']['user']);

		// checking /post/index/user[]:a/user[]:b
		$result = $route->parse('/posts/index/user%5B%5D:a/user%5B%5D:b');
		$this->assertArrayHasKey('user', $result['named']);
		$this->assertEquals(['a', 'b'], $result['named']['user']);
	}

/**
 * test that named params with null/false are excluded
 *
 * @return void
 */
	public function testNamedParamsWithNullFalse() {
		$route = new CakeRoute('/:controller/:action/*');
		$result = $route->match(['controller' => 'posts', 'action' => 'index', 'page' => null, 'sort' => false]);
		$this->assertEquals('/posts/index/', $result);
	}

/**
 * test that match with patterns works.
 *
 * @return void
 */
	public function testMatchWithPatterns() {
		$route = new CakeRoute('/:controller/:action/:id', ['plugin' => null], ['id' => '[0-9]+']);
		$result = $route->match(['controller' => 'posts', 'action' => 'view', 'id' => 'foo']);
		$this->assertFalse($result);

		$result = $route->match(['plugin' => null, 'controller' => 'posts', 'action' => 'view', 'id' => '9']);
		$this->assertEquals('/posts/view/9', $result);

		$result = $route->match(['plugin' => null, 'controller' => 'posts', 'action' => 'view', 'id' => '922']);
		$this->assertEquals('/posts/view/922', $result);

		$result = $route->match(['plugin' => null, 'controller' => 'posts', 'action' => 'view', 'id' => 'a99']);
		$this->assertFalse($result);
	}

/**
 * test persistParams ability to persist parameters from $params and remove params.
 *
 * @return void
 */
	public function testPersistParams() {
		$route = new CakeRoute(
			'/:lang/:color/blog/:action',
			['controller' => 'posts'],
			['persist' => ['lang', 'color']]
		);
		$url = ['controller' => 'posts', 'action' => 'index'];
		$params = ['lang' => 'en', 'color' => 'blue'];
		$result = $route->persistParams($url, $params);
		$this->assertEquals('en', $result['lang']);
		$this->assertEquals('blue', $result['color']);

		$url = ['controller' => 'posts', 'action' => 'index', 'color' => 'red'];
		$params = ['lang' => 'en', 'color' => 'blue'];
		$result = $route->persistParams($url, $params);
		$this->assertEquals('en', $result['lang']);
		$this->assertEquals('red', $result['color']);
	}

/**
 * test persist with a non array value
 *
 * @return void
 */
	public function testPersistParamsNonArray() {
		$url = ['controller' => 'posts', 'action' => 'index'];
		$params = ['lang' => 'en', 'color' => 'blue'];

		$route = new CakeRoute(
			'/:lang/:color/blog/:action',
			['controller' => 'posts']
			// No persist options
		);
		$result = $route->persistParams($url, $params);
		$this->assertEquals($url, $result);

		$route = new CakeRoute(
			'/:lang/:color/blog/:action',
			['controller' => 'posts'],
			['persist' => false]
		);
		$result = $route->persistParams($url, $params);
		$this->assertEquals($url, $result);

		$route = new CakeRoute(
			'/:lang/:color/blog/:action',
			['controller' => 'posts'],
			['persist' => 'derp']
		);
		$result = $route->persistParams($url, $params);
		$this->assertEquals($url, $result);
	}

/**
 * test the parse method of CakeRoute.
 *
 * @return void
 */
	public function testParse() {
		$route = new CakeRoute(
			'/:controller/:action/:id',
			['controller' => 'testing4', 'id' => null],
			['id' => Router::ID]
		);
		$route->compile();
		$result = $route->parse('/posts/view/1');
		$this->assertEquals('posts', $result['controller']);
		$this->assertEquals('view', $result['action']);
		$this->assertEquals('1', $result['id']);

		$route = new Cakeroute(
			'/admin/:controller',
			['prefix' => 'admin', 'admin' => 1, 'action' => 'index']
		);
		$route->compile();
		$result = $route->parse('/admin/');
		$this->assertFalse($result);

		$result = $route->parse('/admin/posts');
		$this->assertEquals('posts', $result['controller']);
		$this->assertEquals('index', $result['action']);
	}

/**
 * Test that :key elements are urldecoded
 *
 * @return void
 */
	public function testParseUrlDecodeElements() {
		$route = new Cakeroute(
			'/:controller/:slug',
			['action' => 'view']
		);
		$route->compile();
		$result = $route->parse('/posts/%E2%88%82%E2%88%82');
		$this->assertEquals('posts', $result['controller']);
		$this->assertEquals('view', $result['action']);
		$this->assertEquals('∂∂', $result['slug']);

		$result = $route->parse('/posts/∂∂');
		$this->assertEquals('posts', $result['controller']);
		$this->assertEquals('view', $result['action']);
		$this->assertEquals('∂∂', $result['slug']);
	}

/**
 * test numerically indexed defaults, get appended to pass
 *
 * @return void
 */
	public function testParseWithPassDefaults() {
		$route = new Cakeroute('/:controller', ['action' => 'display', 'home']);
		$result = $route->parse('/posts');
		$expected = [
			'controller' => 'posts',
			'action' => 'display',
			'pass' => ['home'],
			'named' => []
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test that http header conditions can cause route failures.
 *
 * @return void
 */
	public function testParseWithHttpHeaderConditions() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$route = new CakeRoute('/sample', ['controller' => 'posts', 'action' => 'index', '[method]' => 'POST']);

		$this->assertFalse($route->parse('/sample'));
	}

/**
 * test that patterns work for :action
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
		$this->assertNotEmpty($result);

		$result = $route->parse('/blog/other');
		$expected = ['controller' => 'blog_posts', 'action' => 'other', 'pass' => [], 'named' => []];
		$this->assertEquals($expected, $result);

		$result = $route->parse('/blog/foobar');
		$this->assertFalse($result);
	}

/**
 * test the parseArgs method
 *
 * @return void
 */
	public function testParsePassedArgument() {
		$route = new CakeRoute('/:controller/:action/*');
		$result = $route->parse('/posts/edit/1/2/0');
		$expected = [
			'controller' => 'posts',
			'action' => 'edit',
			'pass' => ['1', '2', '0'],
			'named' => []
		];
		$this->assertEquals($expected, $result);

		$result = $route->parse('/posts/edit/a-string/page:1/sort:value');
		$expected = [
			'controller' => 'posts',
			'action' => 'edit',
			'pass' => ['a-string'],
			'named' => [
				'page' => 1,
				'sort' => 'value'
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test that only named parameter rules are followed.
 *
 * @return void
 */
	public function testParseNamedParametersWithRules() {
		$route = new CakeRoute('/:controller/:action/*', [], [
			'named' => [
				'wibble',
				'fish' => ['action' => 'index'],
				'fizz' => ['controller' => ['comments', 'other']],
				'pattern' => 'val-[\d]+'
			]
		]);
		$result = $route->parse('/posts/display/wibble:spin/fish:trout/fizz:buzz/unknown:value');
		$expected = [
			'controller' => 'posts',
			'action' => 'display',
			'pass' => ['fish:trout', 'fizz:buzz', 'unknown:value'],
			'named' => [
				'wibble' => 'spin'
			]
		];
		$this->assertEquals($expected, $result, 'Fish should not be parsed, as action != index');

		$result = $route->parse('/posts/index/wibble:spin/fish:trout/fizz:buzz');
		$expected = [
			'controller' => 'posts',
			'action' => 'index',
			'pass' => ['fizz:buzz'],
			'named' => [
				'wibble' => 'spin',
				'fish' => 'trout'
			]
		];
		$this->assertEquals($expected, $result, 'Fizz should be parsed, as controller == comments|other');

		$result = $route->parse('/comments/index/wibble:spin/fish:trout/fizz:buzz');
		$expected = [
			'controller' => 'comments',
			'action' => 'index',
			'pass' => [],
			'named' => [
				'wibble' => 'spin',
				'fish' => 'trout',
				'fizz' => 'buzz'
			]
		];
		$this->assertEquals($expected, $result, 'All params should be parsed as conditions were met.');

		$result = $route->parse('/comments/index/pattern:val--');
		$expected = [
			'controller' => 'comments',
			'action' => 'index',
			'pass' => ['pattern:val--'],
			'named' => []
		];
		$this->assertEquals($expected, $result, 'Named parameter pattern unmet.');

		$result = $route->parse('/comments/index/pattern:val-2');
		$expected = [
			'controller' => 'comments',
			'action' => 'index',
			'pass' => [],
			'named' => ['pattern' => 'val-2']
		];
		$this->assertEquals($expected, $result, 'Named parameter pattern met.');
	}

/**
 * test that greedyNamed ignores rules.
 *
 * @return void
 */
	public function testParseGreedyNamed() {
		$route = new CakeRoute('/:controller/:action/*', [], [
			'named' => [
				'fizz' => ['controller' => 'comments'],
				'pattern' => 'val-[\d]+',
			],
			'greedyNamed' => true
		]);
		$result = $route->parse('/posts/display/wibble:spin/fizz:buzz/pattern:ignored');
		$expected = [
			'controller' => 'posts',
			'action' => 'display',
			'pass' => ['fizz:buzz', 'pattern:ignored'],
			'named' => [
				'wibble' => 'spin',
			]
		];
		$this->assertEquals($expected, $result, 'Greedy named grabs everything, rules are followed');
	}

/**
 * Having greedNamed enabled should not capture routing.prefixes.
 *
 * @return void
 */
	public function testMatchGreedyNamedExcludesPrefixes() {
		Configure::write('Routing.prefixes', ['admin']);
		Router::reload();

		$route = new CakeRoute('/sales/*', ['controller' => 'sales', 'action' => 'index']);
		$this->assertFalse($route->match(['controller' => 'sales', 'action' => 'index', 'admin' => 1]), 'Greedy named consume routing prefixes.');
	}

/**
 * test that parsing array format named parameters works
 *
 * @return void
 */
	public function testParseArrayNamedParameters() {
		$route = new CakeRoute('/:controller/:action/*');
		$result = $route->parse('/tests/action/var[]:val1/var[]:val2');
		$expected = [
			'controller' => 'tests',
			'action' => 'action',
			'named' => [
				'var' => [
					'val1',
					'val2'
				]
			],
			'pass' => [],
		];
		$this->assertEquals($expected, $result);

		$result = $route->parse('/tests/action/theanswer[is]:42/var[]:val2/var[]:val3');
		$expected = [
			'controller' => 'tests',
			'action' => 'action',
			'named' => [
				'theanswer' => [
					'is' => 42
				],
				'var' => [
					'val2',
					'val3'
				]
			],
			'pass' => [],
		];
		$this->assertEquals($expected, $result);

		$result = $route->parse('/tests/action/theanswer[is][not]:42/theanswer[]:5/theanswer[is]:6');
		$expected = [
			'controller' => 'tests',
			'action' => 'action',
			'named' => [
				'theanswer' => [
					5,
					'is' => [
						6,
						'not' => 42
					]
				],
			],
			'pass' => [],
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Test that match can handle array named parameters
 *
 * @return void
 */
	public function testMatchNamedParametersArray() {
		$route = new CakeRoute('/:controller/:action/*');

		$url = [
			'controller' => 'posts',
			'action' => 'index',
			'filter' => [
				'one',
				'model' => 'value'
			]
		];
		$result = $route->match($url);
		$expected = '/posts/index/filter%5B0%5D:one/filter%5Bmodel%5D:value';
		$this->assertEquals($expected, $result);

		$url = [
			'controller' => 'posts',
			'action' => 'index',
			'filter' => [
				'one',
				'model' => [
					'two',
					'order' => 'field'
				]
			]
		];
		$result = $route->match($url);
		$expected = '/posts/index/filter%5B0%5D:one/filter%5Bmodel%5D%5B0%5D:two/filter%5Bmodel%5D%5Border%5D:field';
		$this->assertEquals($expected, $result);
	}

/**
 * Test matching of parameters where one parameter name starts with another parameter name
 *
 * @return void
 */
	public function testMatchSimilarParameters() {
		$route = new CakeRoute('/:thisParam/:thisParamIsLonger');

		$url = [
			'thisParamIsLonger' => 'bar',
			'thisParam' => 'foo',
		];

		$result = $route->match($url);
		$expected = '/foo/bar';
		$this->assertEquals($expected, $result);
	}

/**
 * Test match() with trailing ** style routes.
 *
 * @return void
 */
	public function testMatchTrailing() {
		$route = new CakeRoute('/pages/**', ['controller' => 'pages', 'action' => 'display']);
		$id = 'test/ spaces/漢字/la†în';
		$result = $route->match([
			'controller' => 'pages',
			'action' => 'display',
			$id
		]);
		$expected = '/pages/test/%20spaces/%E6%BC%A2%E5%AD%97/la%E2%80%A0%C3%AEn';
		$this->assertEquals($expected, $result);
	}

/**
 * test restructuring args with pass key
 *
 * @return void
 */
	public function testPassArgRestructure() {
		$route = new CakeRoute('/:controller/:action/:slug', [], [
			'pass' => ['slug']
		]);
		$result = $route->parse('/posts/view/my-title');
		$expected = [
			'controller' => 'posts',
			'action' => 'view',
			'slug' => 'my-title',
			'pass' => ['my-title'],
			'named' => []
		];
		$this->assertEquals($expected, $result, 'Slug should have moved');
	}

/**
 * Test the /** special type on parsing.
 *
 * @return void
 */
	public function testParseTrailing() {
		$route = new CakeRoute('/:controller/:action/**');
		$result = $route->parse('/posts/index/1/2/3/foo:bar');
		$expected = [
			'controller' => 'posts',
			'action' => 'index',
			'pass' => ['1/2/3/foo:bar'],
			'named' => []
		];
		$this->assertEquals($expected, $result);

		$result = $route->parse('/posts/index/http://example.com');
		$expected = [
			'controller' => 'posts',
			'action' => 'index',
			'pass' => ['http://example.com'],
			'named' => []
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Test the /** special type on parsing - UTF8.
 *
 * @return void
 */
	public function testParseTrailingUTF8() {
		$route = new CakeRoute('/category/**', ['controller' => 'categories', 'action' => 'index']);
		$result = $route->parse('/category/%D9%85%D9%88%D8%A8%D8%A7%DB%8C%D9%84');
		$expected = [
			'controller' => 'categories',
			'action' => 'index',
			'pass' => ['موبایل'],
			'named' => []
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test that utf-8 patterns work for :section
 *
 * @return void
 */
	public function testUTF8PatternOnSection() {
		$route = new CakeRoute(
			'/:section',
			['plugin' => 'blogs', 'controller' => 'posts', 'action' => 'index'],
			[
				'persist' => ['section'],
				'section' => 'آموزش|weblog'
			]
		);

		$result = $route->parse('/%D8%A2%D9%85%D9%88%D8%B2%D8%B4');
		$expected = ['section' => 'آموزش', 'plugin' => 'blogs', 'controller' => 'posts', 'action' => 'index', 'pass' => [], 'named' => []];
		$this->assertEquals($expected, $result);

		$result = $route->parse('/weblog');
		$expected = ['section' => 'weblog', 'plugin' => 'blogs', 'controller' => 'posts', 'action' => 'index', 'pass' => [], 'named' => []];
		$this->assertEquals($expected, $result);
	}

/**
 * Test for __set_state magic method on CakeRoute
 *
 * @return void
 */
	public function testSetState() {
		$route = CakeRoute::__set_state([
			'keys' => [],
			'options' => [],
			'defaults' => [
				'controller' => 'pages',
				'action' => 'display',
				'home',
			],
			'template' => '/',
			'_greedy' => false,
			'_compiledRoute' => null,
			'_headerMap' => [
				'type' => 'content_type',
				'method' => 'request_method',
				'server' => 'server_name',
			],
		]);
		$this->assertInstanceOf('CakeRoute', $route);
		$this->assertSame('/', $route->match(['controller' => 'pages', 'action' => 'display', 'home']));
		$this->assertFalse($route->match(['controller' => 'pages', 'action' => 'display', 'about']));
		$expected = ['controller' => 'pages', 'action' => 'display', 'pass' => ['home'], 'named' => []];
		$this->assertEquals($expected, $route->parse('/'));
	}

}
