<?php
/**
 * PaginatorHelperTest file
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.Test.Case.View.Helper
 * @since         CakePHP(tm) v 1.2.0.4206
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('View', 'View');
App::uses('HtmlHelper', 'View/Helper');
App::uses('JsHelper', 'View/Helper');
App::uses('PaginatorHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');

if (!defined('FULL_BASE_URL')) {
	define('FULL_BASE_URL', 'http://cakephp.org');
}

/**
 * PaginatorHelperTest class
 *
 * @package       Cake.Test.Case.View.Helper
 */
class PaginatorHelperTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Config.language', 'eng');
		$controller = null;
		$this->View = new View($controller);
		$this->Paginator = new PaginatorHelper($this->View);
		$this->Paginator->Js = $this->getMock('PaginatorHelper', [], [$this->View]);
		$this->Paginator->request = new CakeRequest(null, false);
		$this->Paginator->request->addParams([
			'paging' => [
				'Article' => [
					'page' => 2,
					'current' => 9,
					'count' => 62,
					'prevPage' => false,
					'nextPage' => true,
					'pageCount' => 7,
					'order' => null,
					'limit' => 20,
					'options' => [
						'page' => 1,
						'conditions' => []
					],
					'paramType' => 'named'
				]
			]
		]);
		$this->Paginator->Html = new HtmlHelper($this->View);

		Configure::write('Routing.prefixes', []);
		Router::reload();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->View, $this->Paginator);
	}

/**
 * testHasPrevious method
 *
 * @return void
 */
	public function testHasPrevious() {
		$this->assertFalse($this->Paginator->hasPrev());
		$this->Paginator->request->params['paging']['Article']['prevPage'] = true;
		$this->assertTrue($this->Paginator->hasPrev());
		$this->Paginator->request->params['paging']['Article']['prevPage'] = false;
	}

/**
 * testHasNext method
 *
 * @return void
 */
	public function testHasNext() {
		$this->assertTrue($this->Paginator->hasNext());
		$this->Paginator->request->params['paging']['Article']['nextPage'] = false;
		$this->assertFalse($this->Paginator->hasNext());
		$this->Paginator->request->params['paging']['Article']['nextPage'] = true;
	}

/**
 * testDisabledLink method
 *
 * @return void
 */
	public function testDisabledLink() {
		$this->Paginator->request->params['paging']['Article']['nextPage'] = false;
		$this->Paginator->request->params['paging']['Article']['page'] = 1;
		$result = $this->Paginator->next('Next', [], true);
		$expected = '<span class="next">Next</span>';
		$this->assertEquals($expected, $result);

		$this->Paginator->request->params['paging']['Article']['prevPage'] = false;
		$result = $this->Paginator->prev('prev', ['update' => 'theList', 'indicator' => 'loading', 'url' => ['controller' => 'posts']], null, ['class' => 'disabled', 'tag' => 'span']);
		$expected = [
			'span' => ['class' => 'disabled'], 'prev', '/span'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testSortLinks method
 *
 * @return void
 */
	public function testSortLinks() {
		Router::reload();
		Router::parse('/');
		Router::setRequestInfo([
			['plugin' => null, 'controller' => 'accounts', 'action' => 'index', 'pass' => [], 'url' => ['url' => 'accounts/']],
			['base' => '/officespace', 'here' => '/officespace/accounts/', 'webroot' => '/officespace/']
		]);
		$this->Paginator->options(['url' => ['param']]);
		$this->Paginator->request['paging'] = [
			'Article' => [
				'current' => 9,
				'count' => 62,
				'prevPage' => false,
				'nextPage' => true,
				'pageCount' => 7,
				'options' => [
					'page' => 1,
					'order' => ['date' => 'asc'],
					'conditions' => []
				],
				'paramType' => 'named'
			]
		];

		$result = $this->Paginator->sort('title');
		$expected = [
			'a' => ['href' => '/officespace/accounts/index/param/sort:title/direction:asc'],
			'Title',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->sort('date');
		$expected = [
			'a' => ['href' => '/officespace/accounts/index/param/sort:date/direction:desc', 'class' => 'asc'],
			'Date',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->sort('title', 'TestTitle');
		$expected = [
			'a' => ['href' => '/officespace/accounts/index/param/sort:title/direction:asc'],
			'TestTitle',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->sort('title', ['asc' => 'ascending', 'desc' => 'descending']);
		$expected = [
			'a' => ['href' => '/officespace/accounts/index/param/sort:title/direction:asc'],
			'ascending',
			'/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Article']['options']['sort'] = 'title';
		$result = $this->Paginator->sort('title', ['asc' => 'ascending', 'desc' => 'descending']);
		$expected = [
			'a' => ['href' => '/officespace/accounts/index/param/sort:title/direction:desc', 'class' => 'asc'],
			'descending',
			'/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'desc'];
		$this->Paginator->request->params['paging']['Article']['options']['sort'] = null;
		$result = $this->Paginator->sort('title');
		$this->assertRegExp('/\/accounts\/index\/param\/sort:title\/direction:asc" class="desc">Title<\/a>$/', $result);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'asc'];
		$this->Paginator->request->params['paging']['Article']['options']['sort'] = null;
		$result = $this->Paginator->sort('title');
		$this->assertRegExp('/\/accounts\/index\/param\/sort:title\/direction:desc" class="asc">Title<\/a>$/', $result);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'desc'];
		$this->Paginator->request->params['paging']['Article']['options']['sort'] = null;
		$result = $this->Paginator->sort('title', 'Title', ['direction' => 'desc']);
		$this->assertRegExp('/\/accounts\/index\/param\/sort:title\/direction:asc" class="desc">Title<\/a>$/', $result);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'desc'];
		$this->Paginator->request->params['paging']['Article']['options']['sort'] = null;
		$result = $this->Paginator->sort('title', 'Title', ['direction' => 'ASC']);
		$this->assertRegExp('/\/accounts\/index\/param\/sort:title\/direction:asc" class="desc">Title<\/a>$/', $result);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'asc'];
		$this->Paginator->request->params['paging']['Article']['options']['sort'] = null;
		$result = $this->Paginator->sort('title', 'Title', ['direction' => 'asc']);
		$this->assertRegExp('/\/accounts\/index\/param\/sort:title\/direction:desc" class="asc">Title<\/a>$/', $result);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'asc'];
		$this->Paginator->request->params['paging']['Article']['options']['sort'] = null;
		$result = $this->Paginator->sort('title', 'Title', ['direction' => 'desc']);
		$this->assertRegExp('/\/accounts\/index\/param\/sort:title\/direction:desc" class="asc">Title<\/a>$/', $result);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'asc'];
		$this->Paginator->request->params['paging']['Article']['options']['sort'] = null;
		$result = $this->Paginator->sort('title', 'Title', ['direction' => 'desc', 'class' => 'foo']);
		$this->assertRegExp('/\/accounts\/index\/param\/sort:title\/direction:desc" class="foo asc">Title<\/a>$/', $result);
	}

/**
 * testSortLinksWithLockOption method
 *
 * @return void
 */
	public function testSortLinksWithLockOption() {
		Router::reload();
		Router::parse('/');
		Router::setRequestInfo([
			['plugin' => null, 'controller' => 'accounts', 'action' => 'index', 'pass' => [], 'url' => ['url' => 'accounts/']],
			['base' => '/officespace', 'here' => '/officespace/accounts/', 'webroot' => '/officespace/']
		]);
		$this->Paginator->options(['url' => ['param']]);
		$this->Paginator->request['paging'] = [
			'Article' => [
				'current' => 9,
				'count' => 62,
				'prevPage' => false,
				'nextPage' => true,
				'pageCount' => 7,
				'options' => [
					'page' => 1,
					'order' => ['date' => 'asc'],
					'conditions' => []
				],
				'paramType' => 'named'
			]
		];

		$result = $this->Paginator->sort('distance', null, ['lock' => true]);
		$expected = [
			'a' => ['href' => '/officespace/accounts/index/param/sort:distance/direction:asc'],
			'Distance',
			'/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Article']['options']['sort'] = 'distance';
		$result = $this->Paginator->sort('distance', null, ['lock' => true]);
		$expected = [
			'a' => ['href' => '/officespace/accounts/index/param/sort:distance/direction:asc', 'class' => 'asc locked'],
			'Distance',
			'/a'
		];
		$this->assertTags($result, $expected);
	}

/**
 * test that sort() works with virtual field order options.
 *
 * @return void
 */
	public function testSortLinkWithVirtualField() {
		Router::setRequestInfo([
			['plugin' => null, 'controller' => 'accounts', 'action' => 'index', 'pass' => [], 'form' => [], 'url' => ['url' => 'accounts/']],
			['base' => '', 'here' => '/accounts/', 'webroot' => '/']
		]);
		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['full_name' => 'asc'];

		$result = $this->Paginator->sort('Article.full_name');
		$expected = [
			'a' => ['href' => '/accounts/index/sort:Article.full_name/direction:desc', 'class' => 'asc'],
			'Article Full Name',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->sort('full_name');
		$expected = [
			'a' => ['href' => '/accounts/index/sort:full_name/direction:desc', 'class' => 'asc'],
			'Full Name',
			'/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['full_name' => 'desc'];
		$result = $this->Paginator->sort('Article.full_name');
		$expected = [
			'a' => ['href' => '/accounts/index/sort:Article.full_name/direction:asc', 'class' => 'desc'],
			'Article Full Name',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->sort('full_name');
		$expected = [
			'a' => ['href' => '/accounts/index/sort:full_name/direction:asc', 'class' => 'desc'],
			'Full Name',
			'/a'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testSortLinksUsingDirectionOption method
 *
 * @return void
 */
	public function testSortLinksUsingDirectionOption() {
		Router::reload();
		Router::parse('/');
		Router::setRequestInfo([
			['plugin' => null, 'controller' => 'accounts', 'action' => 'index', 'pass' => [],
				'url' => ['url' => 'accounts/', 'mod_rewrite' => 'true']],
			['base' => '/', 'here' => '/accounts/', 'webroot' => '/']
		]);
		$this->Paginator->options(['url' => ['param']]);

		$result = $this->Paginator->sort('title', 'TestTitle', ['direction' => 'desc']);
		$expected = [
			'a' => ['href' => '/accounts/index/param/sort:title/direction:desc'],
			'TestTitle',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->sort('title', ['asc' => 'ascending', 'desc' => 'descending'], ['direction' => 'desc']);
		$expected = [
			'a' => ['href' => '/accounts/index/param/sort:title/direction:desc'],
			'descending',
			'/a'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testSortLinksUsingDotNotation method
 *
 * @return void
 */
	public function testSortLinksUsingDotNotation() {
		Router::reload();
		Router::parse('/');
		Router::setRequestInfo([
			['plugin' => null, 'controller' => 'accounts', 'action' => 'index', 'pass' => [], 'form' => [], 'url' => ['url' => 'accounts/', 'mod_rewrite' => 'true'], 'bare' => 0],
			['base' => '/officespace', 'here' => '/officespace/accounts/', 'webroot' => '/officespace/']
		]);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'desc'];
		$result = $this->Paginator->sort('Article.title');
		$expected = [
			'a' => ['href' => '/officespace/accounts/index/sort:Article.title/direction:asc', 'class' => 'desc'],
			'Article Title',
			'/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'desc'];
		$result = $this->Paginator->sort('Article.title', 'Title');
		$expected = [
			'a' => ['href' => '/officespace/accounts/index/sort:Article.title/direction:asc', 'class' => 'desc'],
			'Title',
			'/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'asc'];
		$result = $this->Paginator->sort('Article.title', 'Title');
		$expected = [
			'a' => ['href' => '/officespace/accounts/index/sort:Article.title/direction:desc', 'class' => 'asc'],
			'Title',
			'/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Account.title' => 'asc'];
		$result = $this->Paginator->sort('title');
		$expected = [
			'a' => ['href' => '/officespace/accounts/index/sort:title/direction:asc'],
			'Title',
			'/a'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testSortKey method
 *
 * @return void
 */
	public function testSortKey() {
		$result = $this->Paginator->sortKey(null, [
			'order' => ['Article.title' => 'desc'
		]]);
		$this->assertEquals('Article.title', $result);

		$result = $this->Paginator->sortKey('Article', ['order' => 'Article.title']);
		$this->assertEquals('Article.title', $result);

		$result = $this->Paginator->sortKey('Article', ['sort' => 'Article.title']);
		$this->assertEquals('Article.title', $result);

		$result = $this->Paginator->sortKey('Article', ['sort' => 'Article']);
		$this->assertEquals('Article', $result);
	}

/**
 * Test that sortKey falls back to the default sorting options set
 * in the $params which are the default pagination options.
 *
 * @return void
 */
	public function testSortKeyFallbackToParams() {
		$this->Paginator->request->params['paging']['Article']['order'] = 'Article.body';
		$result = $this->Paginator->sortKey();
		$this->assertEquals('Article.body', $result);

		$result = $this->Paginator->sortKey('Article');
		$this->assertEquals('Article.body', $result);

		$this->Paginator->request->params['paging']['Article']['order'] = [
			'Article.body' => 'DESC'
		];
		$result = $this->Paginator->sortKey();
		$this->assertEquals('Article.body', $result);

		$result = $this->Paginator->sortKey('Article');
		$this->assertEquals('Article.body', $result);
	}

/**
 * testSortDir method
 *
 * @return void
 */
	public function testSortDir() {
		$result = $this->Paginator->sortDir();
		$expected = 'asc';

		$this->assertEquals($expected, $result);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'desc'];
		$result = $this->Paginator->sortDir();
		$expected = 'desc';

		$this->assertEquals($expected, $result);

		unset($this->Paginator->request->params['paging']['Article']['options']);
		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'asc'];
		$result = $this->Paginator->sortDir();
		$expected = 'asc';

		$this->assertEquals($expected, $result);

		unset($this->Paginator->request->params['paging']['Article']['options']);
		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['title' => 'desc'];
		$result = $this->Paginator->sortDir();
		$expected = 'desc';

		$this->assertEquals($expected, $result);

		unset($this->Paginator->request->params['paging']['Article']['options']);
		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['title' => 'asc'];
		$result = $this->Paginator->sortDir();
		$expected = 'asc';

		$this->assertEquals($expected, $result);

		unset($this->Paginator->request->params['paging']['Article']['options']);
		$this->Paginator->request->params['paging']['Article']['options']['direction'] = 'asc';
		$result = $this->Paginator->sortDir();
		$expected = 'asc';

		$this->assertEquals($expected, $result);

		unset($this->Paginator->request->params['paging']['Article']['options']);
		$this->Paginator->request->params['paging']['Article']['options']['direction'] = 'desc';
		$result = $this->Paginator->sortDir();
		$expected = 'desc';

		$this->assertEquals($expected, $result);

		unset($this->Paginator->request->params['paging']['Article']['options']);
		$result = $this->Paginator->sortDir('Article', ['direction' => 'asc']);
		$expected = 'asc';

		$this->assertEquals($expected, $result);

		$result = $this->Paginator->sortDir('Article', ['direction' => 'desc']);
		$expected = 'desc';

		$this->assertEquals($expected, $result);

		$result = $this->Paginator->sortDir('Article', ['direction' => 'asc']);
		$expected = 'asc';

		$this->assertEquals($expected, $result);
	}

/**
 * Test that sortDir falls back to the default sorting options set
 * in the $params which are the default pagination options.
 *
 * @return void
 */
	public function testSortDirFallbackToParams() {
		$this->Paginator->request->params['paging']['Article']['order'] = [
			'Article.body' => 'ASC'
		];
		$result = $this->Paginator->sortDir();
		$this->assertEquals('asc', $result);

		$result = $this->Paginator->sortDir('Article');
		$this->assertEquals('asc', $result);

		$this->Paginator->request->params['paging']['Article']['order'] = [
			'Article.body' => 'DESC'
		];
		$result = $this->Paginator->sortDir();
		$this->assertEquals('desc', $result);

		$result = $this->Paginator->sortDir('Article');
		$this->assertEquals('desc', $result);
	}

/**
 * testSortAdminLinks method
 *
 * @return void
 */
	public function testSortAdminLinks() {
		Configure::write('Routing.prefixes', ['admin']);

		Router::reload();
		Router::setRequestInfo([
			['pass' => [], 'named' => [], 'controller' => 'users', 'plugin' => null, 'action' => 'admin_index', 'prefix' => 'admin', 'admin' => true, 'url' => ['ext' => 'html', 'url' => 'admin/users']],
			['base' => '', 'here' => '/admin/users', 'webroot' => '/']
		]);
		Router::parse('/admin/users');
		$this->Paginator->request->params['paging']['Article']['page'] = 1;
		$result = $this->Paginator->next('Next');
		$expected = [
			'span' => ['class' => 'next'],
			'a' => ['href' => '/admin/users/index/page:2', 'rel' => 'next'],
			'Next',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		Router::reload();
		Router::setRequestInfo([
			['plugin' => null, 'controller' => 'test', 'action' => 'admin_index', 'pass' => [], 'prefix' => 'admin', 'admin' => true, 'url' => ['url' => 'admin/test']],
			['base' => '', 'here' => '/admin/test', 'webroot' => '/']
		]);
		Router::parse('/');
		$this->Paginator->options(['url' => ['param']]);
		$result = $this->Paginator->sort('title');
		$expected = [
			'a' => ['href' => '/admin/test/index/param/sort:title/direction:asc'],
			'Title',
			'/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->options(['url' => ['param']]);
		$result = $this->Paginator->sort('Article.title', 'Title');
		$expected = [
			'a' => ['href' => '/admin/test/index/param/sort:Article.title/direction:asc'],
			'Title',
			'/a'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testUrlGeneration method
 *
 * @return void
 */
	public function testUrlGeneration() {
		$result = $this->Paginator->sort('controller');
		$expected = [
			'a' => ['href' => '/index/sort:controller/direction:asc'],
			'Controller',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->url();
		$this->assertEquals('/', $result);

		$this->Paginator->request->params['paging']['Article']['options']['page'] = 2;
		$result = $this->Paginator->url();
		$this->assertEquals('/index/page:2', $result);

		$options = ['order' => ['Article' => 'desc']];
		$result = $this->Paginator->url($options);
		$this->assertEquals('/index/page:2/sort:Article/direction:desc', $result);

		$this->Paginator->request->params['paging']['Article']['options']['page'] = 3;
		$options = ['order' => ['Article.name' => 'desc']];
		$result = $this->Paginator->url($options);
		$this->assertEquals('/index/page:3/sort:Article.name/direction:desc', $result);
	}

/**
 * test URL generation with prefix routes
 *
 * @return void
 */
	public function testUrlGenerationWithPrefixes() {
		Configure::write('Routing.prefixes', ['members']);
		Router::reload();

		Router::parse('/');

		Router::setRequestInfo([
			['controller' => 'posts', 'action' => 'index', 'form' => [], 'url' => [], 'plugin' => null],
			['base' => '', 'here' => 'posts/index', 'webroot' => '/']
		]);

		$this->Paginator->request->params['paging']['Article']['options']['page'] = 2;
		$this->Paginator->request->params['paging']['Article']['page'] = 2;
		$this->Paginator->request->params['paging']['Article']['prevPage'] = true;
		$options = ['members' => true];

		$result = $this->Paginator->url($options);
		$expected = '/members/posts/index/page:2';
		$this->assertEquals($expected, $result);

		$result = $this->Paginator->sort('name', null, ['url' => $options]);
		$expected = [
			'a' => ['href' => '/members/posts/index/page:2/sort:name/direction:asc'],
			'Name',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->next('next', ['url' => $options]);
		$expected = [
			'span' => ['class' => 'next'],
			'a' => ['href' => '/members/posts/index/page:3', 'rel' => 'next'],
			'next',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev('prev', ['url' => $options]);
		$expected = [
			'span' => ['class' => 'prev'],
			'a' => ['href' => '/members/posts', 'rel' => 'prev'],
			'prev',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$options = ['members' => true, 'controller' => 'posts', 'order' => ['name' => 'desc']];
		$result = $this->Paginator->url($options);
		$expected = '/members/posts/index/page:2/sort:name/direction:desc';
		$this->assertEquals($expected, $result);

		$options = ['controller' => 'posts', 'order' => ['Article.name' => 'desc']];
		$result = $this->Paginator->url($options);
		$expected = '/posts/index/page:2/sort:Article.name/direction:desc';
		$this->assertEquals($expected, $result);
	}

/**
 * testOptions method
 *
 * @return void
 */
	public function testOptions() {
		$this->Paginator->options('myDiv');
		$this->assertEquals('myDiv', $this->Paginator->options['update']);

		$this->Paginator->options = [];
		$this->Paginator->request->params = [];

		$options = ['paging' => ['Article' => [
			'order' => 'desc',
			'sort' => 'title'
		]]];
		$this->Paginator->options($options);

		$expected = ['Article' => [
			'order' => 'desc',
			'sort' => 'title'
		]];
		$this->assertEquals($expected, $this->Paginator->request->params['paging']);

		$this->Paginator->options = [];
		$this->Paginator->request->params = [];

		$options = ['Article' => [
			'order' => 'desc',
			'sort' => 'title'
		]];
		$this->Paginator->options($options);
		$this->assertEquals($expected, $this->Paginator->request->params['paging']);

		$options = ['paging' => ['Article' => [
			'order' => 'desc',
			'sort' => 'Article.title'
		]]];
		$this->Paginator->options($options);

		$expected = ['Article' => [
			'order' => 'desc',
			'sort' => 'Article.title'
		]];
		$this->assertEquals($expected, $this->Paginator->request->params['paging']);
	}

/**
 * testPassedArgsMergingWithUrlOptions method
 *
 * @return void
 */
	public function testPassedArgsMergingWithUrlOptions() {
		Router::reload();
		Router::parse('/');
		Router::setRequestInfo([
			['plugin' => null, 'controller' => 'articles', 'action' => 'index', 'pass' => ['2'], 'named' => ['foo' => 'bar'], 'url' => ['url' => 'articles/index/2/foo:bar']],
			['base' => '/', 'here' => '/articles/', 'webroot' => '/']
		]);
		$this->Paginator->request->params['paging'] = [
			'Article' => [
				'page' => 1, 'current' => 3, 'count' => 13,
				'prevPage' => false, 'nextPage' => true, 'pageCount' => 8,
				'options' => [
					'page' => 1,
					'order' => [],
					'conditions' => []
				],
				'paramType' => 'named'
			]
		];

		$this->Paginator->request->params['pass'] = [2];
		$this->Paginator->request->params['named'] = ['foo' => 'bar'];
		$this->Paginator->request->query = ['x' => 'y'];
		$this->Paginator->beforeRender('posts/index');

		$result = $this->Paginator->sort('title');
		$expected = [
			'a' => ['href' => '/articles/index/2/foo:bar/sort:title/direction:asc?x=y'],
			'Title',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->numbers();
		$expected = [
			['span' => ['class' => 'current']], '1', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/articles/index/2/page:2/foo:bar?x=y']], '2', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/articles/index/2/page:3/foo:bar?x=y']], '3', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/articles/index/2/page:4/foo:bar?x=y']], '4', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/articles/index/2/page:5/foo:bar?x=y']], '5', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/articles/index/2/page:6/foo:bar?x=y']], '6', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/articles/index/2/page:7/foo:bar?x=y']], '7', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->next('Next');
		$expected = [
			'span' => ['class' => 'next'],
			'a' => ['href' => '/articles/index/2/page:2/foo:bar?x=y', 'rel' => 'next'],
			'Next',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testPassedArgsMergingWithUrlOptionsParamTypeQuerystring method
 *
 * @return void
 */
	public function testPassedArgsMergingWithUrlOptionsParamTypeQuerystring() {
		Router::reload();
		Router::parse('/');
		Router::setRequestInfo([
			['plugin' => null, 'controller' => 'articles', 'action' => 'index', 'pass' => ['2'], 'named' => ['foo' => 'bar'], 'url' => ['url' => 'articles/index/2/foo:bar']],
			['base' => '/', 'here' => '/articles/', 'webroot' => '/']
		]);
		$this->Paginator->request->params['paging'] = [
			'Article' => [
				'page' => 1, 'current' => 3, 'count' => 13,
				'prevPage' => false, 'nextPage' => true, 'pageCount' => 8,
				'options' => [
					'page' => 1,
					'order' => [],
					'conditions' => []
				],
				'paramType' => 'querystring'
			]
		];

		$this->Paginator->request->params['pass'] = [2];
		$this->Paginator->request->params['named'] = ['foo' => 'bar'];
		$this->Paginator->request->query = ['x' => 'y'];
		$this->Paginator->beforeRender('posts/index');

		$result = $this->Paginator->sort('title');
		$expected = [
			'a' => ['href' => '/articles/index/2/foo:bar?x=y&amp;sort=title&amp;direction=asc'],
			'Title',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->numbers();
		$expected = [
			['span' => ['class' => 'current']], '1', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/articles/index/2/foo:bar?x=y&amp;page=2']], '2', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/articles/index/2/foo:bar?x=y&amp;page=3']], '3', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/articles/index/2/foo:bar?x=y&amp;page=4']], '4', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/articles/index/2/foo:bar?x=y&amp;page=5']], '5', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/articles/index/2/foo:bar?x=y&amp;page=6']], '6', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/articles/index/2/foo:bar?x=y&amp;page=7']], '7', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->next('Next');
		$expected = [
			'span' => ['class' => 'next'],
			'a' => ['href' => '/articles/index/2/foo:bar?x=y&amp;page=2', 'rel' => 'next'],
			'Next',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testPagingLinks method
 *
 * @return void
 */
	public function testPagingLinks() {
		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 1,
				'current' => 3,
				'count' => 13,
				'prevPage' => false,
				'nextPage' => true,
				'pageCount' => 5,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];
		$result = $this->Paginator->prev('<< Previous', null, null, ['class' => 'disabled']);
		$expected = [
			'span' => ['class' => 'disabled'],
			'&lt;&lt; Previous',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev('<< Previous', null, null, ['class' => 'disabled', 'tag' => 'div']);
		$expected = [
			'div' => ['class' => 'disabled'],
			'&lt;&lt; Previous',
			'/div'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Client']['page'] = 2;
		$this->Paginator->request->params['paging']['Client']['prevPage'] = true;
		$result = $this->Paginator->prev('<< Previous', null, null, ['class' => 'disabled']);
		$expected = [
			'span' => ['class' => 'prev'],
			'a' => ['href' => '/', 'rel' => 'prev'],
			'&lt;&lt; Previous',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev('<< Previous', ['tag' => false], null, ['class' => 'disabled']);
		$expected = [
			'a' => ['href' => '/', 'rel' => 'prev', 'class' => 'prev'],
			'&lt;&lt; Previous',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev(
			'<< Previous',
			[],
			null,
			['disabledTag' => 'span', 'class' => 'disabled']
		);
		$expected = [
			'span' => ['class' => 'prev'],
			'a' => ['href' => '/', 'rel' => 'prev'],
			'&lt;&lt; Previous',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->next('Next');
		$expected = [
			'span' => ['class' => 'next'],
			'a' => ['href' => '/index/page:3', 'rel' => 'next'],
			'Next',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->next('Next', ['tag' => 'li']);
		$expected = [
			'li' => ['class' => 'next'],
			'a' => ['href' => '/index/page:3', 'rel' => 'next'],
			'Next',
			'/a',
			'/li'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->next('Next', ['tag' => false]);
		$expected = [
			'a' => ['href' => '/index/page:3', 'rel' => 'next', 'class' => 'next'],
			'Next',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev('<< Previous', ['escape' => true]);
		$expected = [
			'span' => ['class' => 'prev'],
			'a' => ['href' => '/', 'rel' => 'prev'],
			'&lt;&lt; Previous',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev('<< Previous', ['escape' => false]);
		$expected = [
			'span' => ['class' => 'prev'],
			'a' => ['href' => '/', 'rel' => 'prev'],
			'preg:/<< Previous/',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 1,
				'current' => 1,
				'count' => 13,
				'prevPage' => false,
				'nextPage' => true,
				'pageCount' => 5,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];

		$result = $this->Paginator->prev('<i class="fa fa-angle-left"></i>', ['escape' => false], null, ['class' => 'prev disabled']);
		$expected = [
			'span' => ['class' => 'prev disabled'],
			'i' => ['class' => 'fa fa-angle-left'],
			'/i',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev('<i class="fa fa-angle-left"></i>', ['escape' => false], null, ['escape' => true]);
		$expected = [
			'span' => ['class' => 'prev'],
			'&lt;i class=&quot;fa fa-angle-left&quot;&gt;&lt;/i&gt;',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev('<< Previous', null, '<strong>Disabled</strong>');
		$expected = [
			'span' => ['class' => 'prev'],
			'&lt;strong&gt;Disabled&lt;/strong&gt;',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev('<< Previous', null, '<strong>Disabled</strong>', ['escape' => true]);
		$expected = [
			'span' => ['class' => 'prev'],
			'&lt;strong&gt;Disabled&lt;/strong&gt;',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev('<< Previous', null, '<strong>Disabled</strong>', ['escape' => false]);
		$expected = [
			'span' => ['class' => 'prev'],
			'<strong', 'Disabled', '/strong',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev('<< Previous', ['tag' => false], '<strong>Disabled</strong>');
		$expected = [
			'span' => ['class' => 'prev'],
			'&lt;strong&gt;Disabled&lt;/strong&gt;',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev(
			'<< Previous',
			['tag' => 'li'],
			null,
			['tag' => 'li', 'disabledTag' => 'span', 'class' => 'disabled']
		);
		$expected = [
			'li' => ['class' => 'disabled'],
			'span' => [],
			'&lt;&lt; Previous',
			'/span',
			'/li'
		];
		$this->assertTags($result, $expected);
		$result = $this->Paginator->prev(
			'<< Previous',
			[],
			null,
			['tag' => false, 'disabledTag' => 'span', 'class' => 'disabled']
		);
		$expected = [
			'span' => ['class' => 'disabled'],
			'&lt;&lt; Previous',
			'/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 1,
				'current' => 3,
				'count' => 13,
				'prevPage' => false,
				'nextPage' => true,
				'pageCount' => 5,
				'options' => [
					'page' => 1,
					'limit' => 3,
					'order' => ['Client.name' => 'DESC'],
				],
				'paramType' => 'named'
			]
		];

		$this->Paginator->request->params['paging']['Client']['page'] = 2;
		$this->Paginator->request->params['paging']['Client']['prevPage'] = true;
		$result = $this->Paginator->prev('<< Previous', null, null, ['class' => 'disabled']);
		$expected = [
			'span' => ['class' => 'prev'],
			'a' => [
				'href' => '/index/limit:3/sort:Client.name/direction:DESC',
				'rel' => 'prev'
			],
			'&lt;&lt; Previous',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->next('Next');
		$expected = [
			'span' => ['class' => 'next'],
			'a' => [
				'href' => '/index/page:3/limit:3/sort:Client.name/direction:DESC',
				'rel' => 'next'
			],
			'Next',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 2,
				'current' => 1,
				'count' => 13,
				'prevPage' => true,
				'nextPage' => false,
				'pageCount' => 2,
				'options' => [
					'page' => 2,
					'limit' => 10,
					'order' => [],
					'conditions' => []
				],
				'paramType' => 'named'
			]
		];
		$result = $this->Paginator->prev('Prev');
		$expected = [
			'span' => ['class' => 'prev'],
			'a' => ['href' => '/index/limit:10', 'rel' => 'prev'],
			'Prev',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->next('Next', [], null, ['tag' => false]);
		$expected = [
			'span' => ['class' => 'next'],
			'Next',
			'/span'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 2, 'current' => 1, 'count' => 13, 'prevPage' => true,
				'nextPage' => false, 'pageCount' => 2,
				'defaults' => [],
				'options' => [
					'page' => 2, 'limit' => 10, 'order' => [], 'conditions' => []
				],
				'paramType' => 'named'
			]
		];
		$this->Paginator->options(['url' => [12, 'page' => 3]]);
		$result = $this->Paginator->prev('Prev', ['url' => ['foo' => 'bar']]);
		$expected = [
			'span' => ['class' => 'prev'],
			'a' => ['href' => '/index/12/limit:10/foo:bar', 'rel' => 'prev'],
			'Prev',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);
	}

/**
 * test that __pagingLink methods use $options when $disabledOptions is an empty value.
 * allowing you to use shortcut syntax
 *
 * @return void
 */
	public function testPagingLinksOptionsReplaceEmptyDisabledOptions() {
		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 1,
				'current' => 3,
				'count' => 13,
				'prevPage' => false,
				'nextPage' => true,
				'pageCount' => 5,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];
		$result = $this->Paginator->prev('<< Previous', ['escape' => false]);
		$expected = [
			'span' => ['class' => 'prev'],
			'preg:/<< Previous/',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->next('Next >>', ['escape' => false]);
		$expected = [
			'span' => ['class' => 'next'],
			'a' => ['href' => '/index/page:2', 'rel' => 'next'],
			'preg:/Next >>/',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testPagingLinksNotDefaultModel
 *
 * Test the creation of paging links when the non default model is used.
 *
 * @return void
 */
	public function testPagingLinksNotDefaultModel() {
		// Multiple Model Paginate
		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 1,
				'current' => 3,
				'count' => 13,
				'prevPage' => false,
				'nextPage' => true,
				'pageCount' => 5,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			],
			'Server' => [
				'page' => 1,
				'current' => 1,
				'count' => 5,
				'prevPage' => false,
				'nextPage' => false,
				'pageCount' => 5,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];
		$result = $this->Paginator->sort('title', 'Title', ['model' => 'Client']);
		$expected = [
			'a' => ['href' => '/index/sort:title/direction:asc'],
			'Title',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->next('Next', ['model' => 'Client']);
		$expected = [
			'span' => ['class' => 'next'],
			'a' => ['href' => '/index/page:2', 'rel' => 'next'],
			'Next',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->next('Next', ['model' => 'Server'], 'No Next', ['model' => 'Server']);
		$expected = [
			'span' => ['class' => 'next'], 'No Next', '/span'
		];
		$this->assertTags($result, $expected);
	}

/**
 * Test creating paging links for missing models.
 *
 * @return void
 */
	public function testPagingLinksMissingModel() {
		$result = $this->Paginator->sort('title', 'Title', ['model' => 'Missing']);
		$expected = [
			'a' => ['href' => '/index/sort:title/direction:asc'],
			'Title',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->next('Next', ['model' => 'Missing']);
		$expected = [
			'span' => ['class' => 'next'],
			'a' => ['href' => '/index/page:2', 'rel' => 'next'],
			'Next',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev('Prev', ['model' => 'Missing']);
		$expected = [
			'span' => ['class' => 'prev'],
			'Prev',
			'/span'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testGenericLinks method
 *
 * @return void
 */
	public function testGenericLinks() {
		$result = $this->Paginator->link('Sort by title on page 5', ['sort' => 'title', 'page' => 5, 'direction' => 'desc']);
		$expected = [
			'a' => ['href' => '/index/page:5/sort:title/direction:desc'],
			'Sort by title on page 5',
			'/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Article']['options']['page'] = 2;
		$result = $this->Paginator->link('Sort by title', ['sort' => 'title', 'direction' => 'desc']);
		$expected = [
			'a' => ['href' => '/index/page:2/sort:title/direction:desc'],
			'Sort by title',
			'/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Article']['options']['page'] = 4;
		$result = $this->Paginator->link('Sort by title on page 4', ['sort' => 'Article.title', 'direction' => 'desc']);
		$expected = [
			'a' => ['href' => '/index/page:4/sort:Article.title/direction:desc'],
			'Sort by title on page 4',
			'/a'
		];
		$this->assertTags($result, $expected);
	}

/**
 * Tests generation of generic links with preset options
 *
 * @return void
 */
	public function testGenericLinksWithPresetOptions() {
		$result = $this->Paginator->link('Foo!', ['page' => 1]);
		$this->assertTags($result, ['a' => ['href' => '/'], 'Foo!', '/a']);

		$this->Paginator->options(['sort' => 'title', 'direction' => 'desc']);
		$result = $this->Paginator->link('Foo!', ['page' => 1]);
		$this->assertTags($result, [
			'a' => [
				'href' => '/',
				'sort' => 'title',
				'direction' => 'desc'
			],
			'Foo!',
			'/a'
		]);

		$this->Paginator->options(['sort' => null, 'direction' => null]);
		$result = $this->Paginator->link('Foo!', ['page' => 1]);
		$this->assertTags($result, ['a' => ['href' => '/'], 'Foo!', '/a']);

		$this->Paginator->options(['url' => [
			'sort' => 'title',
			'direction' => 'desc'
		]]);
		$result = $this->Paginator->link('Foo!', ['page' => 1]);
		$this->assertTags($result, [
			'a' => ['href' => '/index/sort:title/direction:desc'],
			'Foo!',
			'/a'
		]);
	}

/**
 * testNumbers method
 *
 * @return void
 */
	public function testNumbers() {
		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 8,
				'current' => 3,
				'count' => 30,
				'prevPage' => false,
				'nextPage' => 2,
				'pageCount' => 15,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];
		$result = $this->Paginator->numbers();
		$expected = [
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:5']], '5', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:6']], '6', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '8', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:10']], '10', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:11']], '11', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:12']], '12', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->numbers(['tag' => 'li']);
		$expected = [
			['li' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:5']], '5', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:6']], '6', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/li',
			' | ',
			['li' => ['class' => 'current']], '8', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:10']], '10', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:11']], '11', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:12']], '12', '/a', '/li',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->numbers(['tag' => 'li', 'separator' => false]);
		$expected = [
			['li' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/li',
			['li' => []], ['a' => ['href' => '/index/page:5']], '5', '/a', '/li',
			['li' => []], ['a' => ['href' => '/index/page:6']], '6', '/a', '/li',
			['li' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/li',
			['li' => ['class' => 'current']], '8', '/li',
			['li' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/li',
			['li' => []], ['a' => ['href' => '/index/page:10']], '10', '/a', '/li',
			['li' => []], ['a' => ['href' => '/index/page:11']], '11', '/a', '/li',
			['li' => []], ['a' => ['href' => '/index/page:12']], '12', '/a', '/li',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->numbers(true);
		$expected = [
			['span' => []], ['a' => ['href' => '/', 'rel' => 'first']], 'first', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:5']], '5', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:6']], '6', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '8', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:10']], '10', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:11']], '11', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:12']], '12', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:15', 'rel' => 'last']], 'last', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 1,
				'current' => 3,
				'count' => 30,
				'prevPage' => false,
				'nextPage' => 2,
				'pageCount' => 15,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];
		$result = $this->Paginator->numbers();
		$expected = [
			['span' => ['class' => 'current']], '1', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:3']], '3', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:5']], '5', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:6']], '6', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:8']], '8', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 14,
				'current' => 3,
				'count' => 30,
				'prevPage' => false,
				'nextPage' => 2,
				'pageCount' => 15,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];
		$result = $this->Paginator->numbers();
		$expected = [
			['span' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:8']], '8', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:10']], '10', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:11']], '11', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:12']], '12', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:13']], '13', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '14', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:15']], '15', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 2,
				'current' => 3,
				'count' => 27,
				'prevPage' => false,
				'nextPage' => 2,
				'pageCount' => 9,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];

		$result = $this->Paginator->numbers(['first' => 1, 'class' => 'page-link']);
		$expected = [
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/']], '1', '/a', '/span',
			' | ',
			['span' => ['class' => 'current page-link']], '2', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:3']], '3', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:5']], '5', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:6']], '6', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:8']], '8', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:9']], '9', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->numbers(['first' => 1, 'currentClass' => 'active']);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			' | ',
			['span' => ['class' => 'active']], '2', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:3']], '3', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:5']], '5', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:6']], '6', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:8']], '8', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->numbers(['first' => 1, 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a']);
		$expected = [
			['li' => []], ['a' => ['href' => '/']], '1', '/a', '/li',
			' | ',
			['li' => ['class' => 'active']], ['a' => []], '2', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:3']], '3', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:5']], '5', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:6']], '6', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:8']], '8', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/li',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->numbers(['first' => 1, 'class' => 'page-link', 'currentClass' => 'active']);
		$expected = [
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/']], '1', '/a', '/span',
			' | ',
			['span' => ['class' => 'active page-link']], '2', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:3']], '3', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:5']], '5', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:6']], '6', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:8']], '8', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:9']], '9', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->numbers(['last' => 1]);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '2', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:3']], '3', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:5']], '5', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:6']], '6', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:8']], '8', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 15,
				'current' => 3,
				'count' => 30,
				'prevPage' => false,
				'nextPage' => 2,
				'pageCount' => 15,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];

		$result = $this->Paginator->numbers(['first' => 1]);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:8']], '8', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:10']], '10', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:11']], '11', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:12']], '12', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:13']], '13', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:14']], '14', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '15', '/span',

		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 1,
				'current' => 10,
				'count' => 30,
				'prevPage' => false,
				'nextPage' => 2,
				'pageCount' => 3,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];
		$options = ['modulus' => 10];
		$result = $this->Paginator->numbers($options);
		$expected = [
			['span' => ['class' => 'current']], '1', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:3']], '3', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->numbers(['modulus' => 3, 'currentTag' => 'span', 'tag' => 'li']);
		$expected = [
			['li' => ['class' => 'current']], ['span' => []], '1', '/span', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/li',
			' | ',
			['li' => []], ['a' => ['href' => '/index/page:3']], '3', '/a', '/li',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 2,
				'current' => 10,
				'count' => 31,
				'prevPage' => true,
				'nextPage' => true,
				'pageCount' => 4,
				'options' => [
					'page' => 1,
					'order' => ['Client.name' => 'DESC'],
				],
				'paramType' => 'named'
			]
		];
		$result = $this->Paginator->numbers(['class' => 'page-link']);
		$expected = [
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/sort:Client.name/direction:DESC']], '1', '/a', '/span',
			' | ',
			['span' => ['class' => 'current page-link']], '2', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:3/sort:Client.name/direction:DESC']], '3', '/a', '/span',
			' | ',
			['span' => ['class' => 'page-link']], ['a' => ['href' => '/index/page:4/sort:Client.name/direction:DESC']], '4', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 2,
				'current' => 2,
				'count' => 30,
				'prevPage' => false,
				'nextPage' => 3,
				'pageCount' => 3,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];

		$request = new CakeRequest();
		$request->addParams([
			'controller' => 'clients', 'action' => 'index', 'plugin' => null, 'page' => 2
		]);
		$request->base = '';
		$request->here = '/clients/index/page:2';
		$request->webroot = '/';

		Router::setRequestInfo($request);

		$result = $this->Paginator->numbers();
		$expected = [
			['span' => []], ['a' => ['href' => '/clients']], '1', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '2', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/clients/index/page:3']], '3', '/a', '/span',

		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 2,
				'current' => 2,
				'count' => 30,
				'prevPage' => false,
				'nextPage' => 3,
				'pageCount' => 3,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'querystring'
			]
		];

		$request = new CakeRequest();
		$request->addParams([
			'controller' => 'clients', 'action' => 'index', 'plugin' => null
		]);
		$request->base = '';
		$request->here = '/clients?page=2';
		$request->webroot = '/';

		Router::setRequestInfo($request);

		$result = $this->Paginator->numbers();
		$expected = [
			['span' => []], ['a' => ['href' => '/clients']], '1', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '2', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/clients?page=3']], '3', '/a', '/span',

		];
		$this->assertTags($result, $expected);
	}

/**
 * Test that numbers() works with first and last options.
 *
 * @return void
 */
	public function testNumbersFirstAndLast() {
		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 10,
				'current' => 3,
				'count' => 30,
				'prevPage' => false,
				'nextPage' => 2,
				'pageCount' => 15,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];

		$result = $this->Paginator->numbers(['first' => 1, 'last' => 1]);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:6']], '6', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:8']], '8', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '10', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:11']], '11', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:12']], '12', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:13']], '13', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:14']], '14', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:15']], '15', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 6,
				'current' => 15,
				'count' => 623,
				'prevPage' => 1,
				'nextPage' => 1,
				'pageCount' => 42,
				'options' => [
					'page' => 6,
				],
				'paramType' => 'named'
			]
		];

		$result = $this->Paginator->numbers(['first' => 1, 'last' => 1]);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:3']], '3', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:5']], '5', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '6', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:8']], '8', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:10']], '10', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:42']], '42', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 37,
				'current' => 15,
				'count' => 623,
				'prevPage' => 1,
				'nextPage' => 1,
				'pageCount' => 42,
				'options' => [
					'page' => 37,
				],
				'paramType' => 'named'
			]
		];

		$result = $this->Paginator->numbers(['first' => 1, 'last' => 1]);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:33']], '33', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:34']], '34', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:35']], '35', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:36']], '36', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '37', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:38']], '38', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:39']], '39', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:40']], '40', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:41']], '41', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:42']], '42', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 4895,
				'current' => 10,
				'count' => 48962,
				'prevPage' => 1,
				'nextPage' => 1,
				'pageCount' => 4897,
				'options' => [
					'page' => 4894,
				],
				'paramType' => 'named'
			]
		];

		$result = $this->Paginator->numbers(['first' => 2, 'modulus' => 2, 'last' => 2]);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:4894']], '4894', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '4895', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:4896']], '4896', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:4897']], '4897', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Client']['page'] = 3;

		$result = $this->Paginator->numbers(['first' => 2, 'modulus' => 2, 'last' => 2]);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '3', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:4896']], '4896', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:4897']], '4897', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->numbers(['first' => 2, 'modulus' => 2, 'last' => 2, 'separator' => ' - ']);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/span',
			' - ',
			['span' => ['class' => 'current']], '3', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:4896']], '4896', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4897']], '4897', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->numbers(['first' => 5, 'modulus' => 5, 'last' => 5, 'separator' => ' - ']);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/span',
			' - ',
			['span' => ['class' => 'current']], '3', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:5']], '5', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:6']], '6', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:4893']], '4893', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4894']], '4894', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4895']], '4895', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4896']], '4896', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4897']], '4897', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Client']['page'] = 4893;
		$result = $this->Paginator->numbers(['first' => 5, 'modulus' => 4, 'last' => 5, 'separator' => ' - ']);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:3']], '3', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:5']], '5', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:4891']], '4891', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4892']], '4892', '/a', '/span',
			' - ',
			['span' => ['class' => 'current']], '4893', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4894']], '4894', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4895']], '4895', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4896']], '4896', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4897']], '4897', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Client']['page'] = 58;
		$result = $this->Paginator->numbers(['first' => 5, 'modulus' => 4, 'last' => 5, 'separator' => ' - ']);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:3']], '3', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:5']], '5', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:56']], '56', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:57']], '57', '/a', '/span',
			' - ',
			['span' => ['class' => 'current']], '58', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:59']], '59', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:60']], '60', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:4893']], '4893', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4894']], '4894', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4895']], '4895', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4896']], '4896', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4897']], '4897', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Client']['page'] = 5;
		$result = $this->Paginator->numbers(['first' => 5, 'modulus' => 4, 'last' => 5, 'separator' => ' - ']);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:3']], '3', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' - ',
			['span' => ['class' => 'current']], '5', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:6']], '6', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:4893']], '4893', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4894']], '4894', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4895']], '4895', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4896']], '4896', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4897']], '4897', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Client']['page'] = 3;
		$result = $this->Paginator->numbers(['first' => 2, 'modulus' => 2, 'last' => 2, 'separator' => ' - ', 'ellipsis' => ' ~~~ ']);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/span',
			' - ',
			['span' => ['class' => 'current']], '3', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			' ~~~ ',
			['span' => []], ['a' => ['href' => '/index/page:4896']], '4896', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4897']], '4897', '/a', '/span',
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Client']['page'] = 3;
		$result = $this->Paginator->numbers(['first' => 2, 'modulus' => 2, 'last' => 2, 'separator' => ' - ', 'ellipsis' => '<span class="ellipsis">...</span>']);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:2']], '2', '/a', '/span',
			' - ',
			['span' => ['class' => 'current']], '3', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4']], '4', '/a', '/span',
			['span' => ['class' => 'ellipsis']], '...', '/span',
			['span' => []], ['a' => ['href' => '/index/page:4896']], '4896', '/a', '/span',
			' - ',
			['span' => []], ['a' => ['href' => '/index/page:4897']], '4897', '/a', '/span',
		];
		$this->assertTags($result, $expected);
	}

/**
 * Test first/last options as strings.
 *
 * @return void
 */
	public function testNumbersStringFirstAndLast() {
		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 10,
				'current' => 3,
				'count' => 30,
				'prevPage' => false,
				'nextPage' => 2,
				'pageCount' => 15,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named'
			]
		];

		$result = $this->Paginator->numbers(['first' => '1', 'last' => '1']);
		$expected = [
			['span' => []], ['a' => ['href' => '/']], '1', '/a', '/span',
			'...',
			['span' => []], ['a' => ['href' => '/index/page:6']], '6', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:7']], '7', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:8']], '8', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:9']], '9', '/a', '/span',
			' | ',
			['span' => ['class' => 'current']], '10', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:11']], '11', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:12']], '12', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:13']], '13', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:14']], '14', '/a', '/span',
			' | ',
			['span' => []], ['a' => ['href' => '/index/page:15']], '15', '/a', '/span',
		];
		$this->assertTags($result, $expected);
	}

/**
 * test first() and last() with tag options
 *
 * @return void
 */
	public function testFirstAndLastTag() {
		$result = $this->Paginator->first('<<', ['tag' => 'li', 'class' => 'first']);
		$expected = [
			'li' => ['class' => 'first'],
			'a' => ['href' => '/', 'rel' => 'first'],
			'&lt;&lt;',
			'/a',
			'/li'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->last(2, ['tag' => 'li', 'class' => 'last']);
		$expected = [
			'...',
			'li' => ['class' => 'last'],
			['a' => ['href' => '/index/page:6']], '6', '/a',
			'/li',
			' | ',
			['li' => ['class' => 'last']],
			['a' => ['href' => '/index/page:7']], '7', '/a',
			'/li',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->last('2', ['tag' => 'li', 'class' => 'last']);
		$this->assertTags($result, $expected);
	}

/**
 * test that on the last page you don't get a link ot the last page.
 *
 * @return void
 */
	public function testLastNoOutput() {
		$this->Paginator->request->params['paging']['Article']['page'] = 15;
		$this->Paginator->request->params['paging']['Article']['pageCount'] = 15;

		$result = $this->Paginator->last();
		$expected = '';
		$this->assertEquals($expected, $result);
	}

/**
 * test first() on the first page.
 *
 * @return void
 */
	public function testFirstEmpty() {
		$this->Paginator->request->params['paging']['Article']['page'] = 1;

		$result = $this->Paginator->first();
		$expected = '';
		$this->assertEquals($expected, $result);
	}

/**
 * test first() and options()
 *
 * @return void
 */
	public function testFirstFullBaseUrl() {
		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'DESC'];

		$this->Paginator->options(['url' => ['full_base' => true]]);

		$result = $this->Paginator->first();
		$expected = [
			'<span',
			['a' => [
				'href' => FULL_BASE_URL . '/index/sort:Article.title/direction:DESC', 'rel' => 'first'
			]],
			'&lt;&lt; first',
			'/a',
			'/span',
		];
		$this->assertTags($result, $expected);
	}

/**
 * test first() on the fence-post
 *
 * @return void
 */
	public function testFirstBoundaries() {
		$result = $this->Paginator->first();
		$expected = [
			'<span',
			'a' => ['href' => '/', 'rel' => 'first'],
			'&lt;&lt; first',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->first(2);
		$expected = [
			'<span',
			['a' => ['href' => '/']], '1', '/a',
			'/span',
			' | ',
			'<span',
			['a' => ['href' => '/index/page:2']], '2', '/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Article']['page'] = 2;
		$result = $this->Paginator->first(3);
		$this->assertEquals('', $result, 'When inside the first links range, no links should be made');
	}

/**
 * test params() method
 *
 * @return void
 */
	public function testParams() {
		$result = $this->Paginator->params();
		$this->assertArrayHasKey('page', $result);
		$this->assertArrayHasKey('pageCount', $result);
	}

/**
 * test param() method
 *
 * @return void
 */
	public function testParam() {
		$result = $this->Paginator->param('count');
		$this->assertSame(62, $result);

		$result = $this->Paginator->param('imaginary');
		$this->assertNull($result);
	}

/**
 * test last() method
 *
 * @return void
 */
	public function testLast() {
		$result = $this->Paginator->last();
		$expected = [
			'<span',
			'a' => ['href' => '/index/page:7', 'rel' => 'last'],
			'last &gt;&gt;',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->last(1);
		$expected = [
			'...',
			'<span',
			'a' => ['href' => '/index/page:7'],
			'7',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->request->params['paging']['Article']['page'] = 6;

		$result = $this->Paginator->last(2);
		$expected = [
			'...',
			'<span',
			['a' => ['href' => '/index/page:6']], '6', '/a',
			'/span',
			' | ',
			'<span',
			['a' => ['href' => '/index/page:7']], '7', '/a',
			'/span',
		];
		$this->assertTags($result, $expected);

		// Test stringy number.
		$result = $this->Paginator->last('2');
		$this->assertTags($result, $expected);

		$result = $this->Paginator->last(3);
		$this->assertEquals('', $result, 'When inside the last links range, no links should be made');
	}

/**
 * undocumented function
 *
 * @return void
 */
	public function testLastOptions() {
		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 4,
				'current' => 3,
				'count' => 30,
				'prevPage' => false,
				'nextPage' => 2,
				'pageCount' => 15,
				'options' => [
					'page' => 1,
					'order' => ['Client.name' => 'DESC'],
				],
				'paramType' => 'named'
			]
		];

		$result = $this->Paginator->last();
		$expected = [
			'<span',
			['a' => [
				'href' => '/index/page:15/sort:Client.name/direction:DESC',
				'rel' => 'last'
			]],
				'last &gt;&gt;', '/a',
			'/span',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->last(1);
		$expected = [
			'...',
			'<span',
			['a' => ['href' => '/index/page:15/sort:Client.name/direction:DESC']], '15', '/a',
			'/span',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->last(2);
		$expected = [
			'...',
			'<span',
			['a' => ['href' => '/index/page:14/sort:Client.name/direction:DESC']], '14', '/a',
			'/span',
			' | ',
			'<span',
			['a' => ['href' => '/index/page:15/sort:Client.name/direction:DESC']], '15', '/a',
			'/span',
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->last(2, ['ellipsis' => '<span class="ellipsis">...</span>']);
		$expected = [
			['span' => ['class' => 'ellipsis']], '...', '/span',
			'<span',
			['a' => ['href' => '/index/page:14/sort:Client.name/direction:DESC']], '14', '/a',
			'/span',
			' | ',
			'<span',
			['a' => ['href' => '/index/page:15/sort:Client.name/direction:DESC']], '15', '/a',
			'/span',
		];
		$this->assertTags($result, $expected);
	}

/**
 * testCounter method
 *
 * @return void
 */
	public function testCounter() {
		$this->Paginator->request->params['paging'] = [
			'Client' => [
				'page' => 1,
				'current' => 3,
				'count' => 13,
				'prevPage' => false,
				'nextPage' => true,
				'pageCount' => 5,
				'limit' => 3,
				'options' => [
					'page' => 1,
					'order' => ['Client.name' => 'DESC'],
				],
				'paramType' => 'named'
			]
		];
		$input = 'Page %page% of %pages%, showing %current% records out of %count% total, ';
		$input .= 'starting on record %start%, ending on %end%';
		$result = $this->Paginator->counter($input);
		$expected = 'Page 1 of 5, showing 3 records out of 13 total, starting on record 1, ';
		$expected .= 'ending on 3';
		$this->assertEquals($expected, $result);

		$input = 'Page {:page} of {:pages}, showing {:current} records out of {:count} total, ';
		$input .= 'starting on record {:start}, ending on {:end}';
		$result = $this->Paginator->counter($input);
		$this->assertEquals($expected, $result);

		$input = 'Page %page% of %pages%';
		$result = $this->Paginator->counter($input);
		$expected = 'Page 1 of 5';
		$this->assertEquals($expected, $result);

		$result = $this->Paginator->counter(['format' => $input]);
		$expected = 'Page 1 of 5';
		$this->assertEquals($expected, $result);

		$result = $this->Paginator->counter(['format' => 'pages']);
		$expected = '1 of 5';
		$this->assertEquals($expected, $result);

		$result = $this->Paginator->counter(['format' => 'range']);
		$expected = '1 - 3 of 13';
		$this->assertEquals($expected, $result);

		$result = $this->Paginator->counter('Showing %page% of %pages% %model%');
		$this->assertEquals('Showing 1 of 5 clients', $result);
	}

/**
 * testHasPage method
 *
 * @return void
 */
	public function testHasPage() {
		$result = $this->Paginator->hasPage('Article', 15);
		$this->assertFalse($result);

		$result = $this->Paginator->hasPage('UndefinedModel', 2);
		$this->assertFalse($result);

		$result = $this->Paginator->hasPage('Article', 2);
		$this->assertTrue($result);

		$result = $this->Paginator->hasPage(2);
		$this->assertTrue($result);
	}

/**
 * testWithPlugin method
 *
 * @return void
 */
	public function testWithPlugin() {
		Router::reload();
		Router::setRequestInfo([
			[
				'pass' => [], 'named' => [], 'prefix' => null, 'form' => [],
				'controller' => 'magazines', 'plugin' => 'my_plugin', 'action' => 'index',
				'url' => ['ext' => 'html', 'url' => 'my_plugin/magazines']],
			['base' => '', 'here' => '/my_plugin/magazines', 'webroot' => '/']
		]);

		$result = $this->Paginator->link('Page 3', ['page' => 3]);
		$expected = [
			'a' => ['href' => '/my_plugin/magazines/index/page:3'], 'Page 3', '/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->options(['url' => ['action' => 'another_index']]);
		$result = $this->Paginator->link('Page 3', ['page' => 3]);
		$expected = [
			'a' => ['href' => '/my_plugin/magazines/another_index/page:3'], 'Page 3', '/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->options(['url' => ['controller' => 'issues']]);
		$result = $this->Paginator->link('Page 3', ['page' => 3]);
		$expected = [
			'a' => ['href' => '/my_plugin/issues/index/page:3'], 'Page 3', '/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->options(['url' => ['plugin' => null]]);
		$result = $this->Paginator->link('Page 3', ['page' => 3]);
		$expected = [
			'a' => ['href' => '/magazines/index/page:3'], 'Page 3', '/a'
		];
		$this->assertTags($result, $expected);

		$this->Paginator->options(['url' => ['plugin' => null, 'controller' => 'issues']]);
		$result = $this->Paginator->link('Page 3', ['page' => 3]);
		$expected = [
			'a' => ['href' => '/issues/index/page:3'], 'Page 3', '/a'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testNextLinkUsingDotNotation method
 *
 * @return void
 */
	public function testNextLinkUsingDotNotation() {
		Router::reload();
		Router::parse('/');
		Router::setRequestInfo([
			['plugin' => null, 'controller' => 'accounts', 'action' => 'index', 'pass' => [], 'url' => ['url' => 'accounts/']],
			['base' => '/officespace', 'here' => '/officespace/accounts/', 'webroot' => '/officespace/', 'passedArgs' => []]
		]);

		$this->Paginator->request->params['paging']['Article']['options']['order'] = ['Article.title' => 'asc'];
		$this->Paginator->request->params['paging']['Article']['page'] = 1;

		$test = ['url' => [
			'page' => '1',
			'sort' => 'Article.title',
			'direction' => 'asc',
		]];
		$this->Paginator->options($test);

		$result = $this->Paginator->next('Next');
		$expected = [
			'span' => ['class' => 'next'],
			'a' => [
				'href' => '/officespace/accounts/index/page:2/sort:Article.title/direction:asc',
				'rel' => 'next'
			],
			'Next',
			'/a',
			'/span',
		];
		$this->assertTags($result, $expected);
	}

/**
 * Ensure that the internal link class object is called when the update key is present
 *
 * @return void
 */
	public function testAjaxLinkGenerationNumbers() {
		$this->Paginator->Js->expectCallCount('link', 2);
		$this->Paginator->numbers([
			'modulus' => '2',
			'url' => ['controller' => 'projects', 'action' => 'sort'],
			'update' => 'list'
		]);
	}

/**
 * test that paginatorHelper::link() uses JsHelper to make links when 'update' key is present
 *
 * @return void
 */
	public function testAjaxLinkGenerationLink() {
		$this->Paginator->Js->expects($this->once())
			->method('link')
			->will($this->returnValue('I am a link'));

		$result = $this->Paginator->link('test', ['controller' => 'posts'], ['update' => '#content']);
		$this->assertEquals('I am a link', $result);
	}

/**
 * test that mock classes injected into paginatorHelper are called when using link()
 *
 * @expectedException CakeException
 * @return void
 */
	public function testMockAjaxProviderClassInjection() {
		$mock = $this->getMock('PaginatorHelper', [], [$this->View], 'PaginatorMockJsHelper');
		$Paginator = new PaginatorHelper($this->View, ['ajax' => 'PaginatorMockJs']);
		$Paginator->request->params['paging'] = [
			'Article' => [
				'current' => 9,
				'count' => 62,
				'prevPage' => false,
				'nextPage' => true,
				'pageCount' => 7,
				'defaults' => [],
				'options' => [],
				'paramType' => 'named'
			]
		];
		$Paginator->PaginatorMockJs = $mock;
		$Paginator->PaginatorMockJs->expects($this->once())->method('link');
		$Paginator->link('Page 2', ['page' => 2], ['update' => '#content']);

		new PaginatorHelper($this->View, ['ajax' => 'Form']);
	}

/**
 * test that query string URLs can be generated.
 *
 * @return void
 */
	public function testQuerystringUrlGeneration() {
		$this->Paginator->request->params['paging']['Article']['paramType'] = 'querystring';
		$result = $this->Paginator->url(['page' => '1']);
		$expected = '/';
		$this->assertEquals($expected, $result);

		$result = $this->Paginator->url(['page' => '1', 'limit' => 10, 'something' => 'else']);
		$expected = '/index/something:else?limit=10';
		$this->assertEquals($expected, $result);

		$result = $this->Paginator->url(['page' => '4']);
		$expected = '/?page=4';
		$this->assertEquals($expected, $result);

		$result = $this->Paginator->url(['page' => '4', 'limit' => 10, 'something' => 'else']);
		$expected = '/index/something:else?page=4&amp;limit=10';
		$this->assertEquals($expected, $result);
	}

/**
 * test query string paging link.
 *
 * @return void
 */
	public function testQuerystringNextAndPrev() {
		$this->Paginator->request->params['paging']['Article']['paramType'] = 'querystring';
		$this->Paginator->request->params['paging']['Article']['page'] = 2;
		$this->Paginator->request->params['paging']['Article']['nextPage'] = true;
		$this->Paginator->request->params['paging']['Article']['prevPage'] = true;

		$result = $this->Paginator->next('Next');
		$expected = [
			'span' => ['class' => 'next'],
			'a' => ['href' => '/?page=3', 'rel' => 'next'],
			'Next',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);

		$result = $this->Paginator->prev('Prev');
		$expected = [
			'span' => ['class' => 'prev'],
			'a' => ['href' => '/', 'rel' => 'prev'],
			'Prev',
			'/a',
			'/span'
		];
		$this->assertTags($result, $expected);
	}

/**
 * test that additional keys can be flagged as query string args.
 *
 * @return void
 */
	public function testOptionsConvertKeys() {
		$this->Paginator->options([
			'convertKeys' => ['something'],
			'Article' => ['paramType' => 'querystring']
		]);
		$result = $this->Paginator->url(['page' => '4', 'something' => 'bar']);
		$expected = '/?page=4&amp;something=bar';
		$this->assertEquals($expected, $result);
	}

/**
 * test the current() method
 *
 * @return void
 */
	public function testCurrent() {
		$result = $this->Paginator->current();
		$this->assertEquals($this->Paginator->request->params['paging']['Article']['page'], $result);

		$result = $this->Paginator->current('Incorrect');
		$this->assertEquals(1, $result);
	}

/**
 * test the defaultModel() method
 *
 * @return void
 */
	public function testNoDefaultModel() {
		$this->Paginator->request = new CakeRequest(null, false);
		$this->assertNull($this->Paginator->defaultModel());
	}

/**
 * test the numbers() method when there is only one page
 *
 * @return void
 */
	public function testWithOnePage() {
		$this->Paginator->request['paging'] = [
			'Article' => [
				'page' => 1,
				'current' => 2,
				'count' => 2,
				'prevPage' => false,
				'nextPage' => true,
				'pageCount' => 1,
				'options' => [
					'page' => 1,
				],
				'paramType' => 'named',
			]
		];
		$this->assertSame('', $this->Paginator->numbers());
		$this->assertSame('', $this->Paginator->first());
		$this->assertSame('', $this->Paginator->last());
	}

/**
 * test the numbers() method when there is only one page
 *
 * @return void
 */
	public function testWithZeroPages() {
		$this->Paginator->request['paging'] = [
			'Article' => [
				'page' => 0,
				'current' => 0,
				'count' => 0,
				'prevPage' => false,
				'nextPage' => false,
				'pageCount' => 0,
				'limit' => 10,
				'options' => [
					'page' => 0,
					'conditions' => []
				],
				'paramType' => 'named',
			]
		];

		$result = $this->Paginator->counter(['format' => 'pages']);
		$expected = '0 of 1';
		$this->assertEquals($expected, $result);
	}

/**
 * Verify that no next and prev links are created for single page results
 *
 * @return void
 */
	public function testMetaPage0() {
		$this->Paginator->request['paging'] = [
			'Article' => [
				'page' => 1,
				'prevPage' => false,
				'nextPage' => false,
				'pageCount' => 1,
			]
		];
		$expected = '';
		$result = $this->Paginator->meta();
		$this->assertSame($expected, $result);
	}

/**
 * Verify that page 1 only has a next link
 *
 * @return void
 */
	public function testMetaPage1() {
		$this->Paginator->request['paging'] = [
			'Article' => [
				'page' => 1,
				'prevPage' => false,
				'nextPage' => true,
				'pageCount' => 2,
				'options' => [],
				'paramType' => 'querystring'
			]
		];
		$expected = '<link href="/?page=2" rel="next"/>';
		$result = $this->Paginator->meta();
		$this->assertSame($expected, $result);
	}

/**
 * Verify that the method will append to a block
 *
 * @return void
 */
	public function testMetaPage1InlineFalse() {
		$this->Paginator->request['paging'] = [
			'Article' => [
				'page' => 1,
				'prevPage' => false,
				'nextPage' => true,
				'pageCount' => 2,
				'options' => [],
				'paramType' => 'querystring'
			]
		];
		$expected = '<link href="/?page=2" rel="next"/>';
		$this->Paginator->meta(['block' => true]);
		$result = $this->View->fetch('meta');
		$this->assertSame($expected, $result);
	}

/**
 * Verify that the last page only has a prev link
 *
 * @return void
 */
	public function testMetaPage1Last() {
		$this->Paginator->request['paging'] = [
			'Article' => [
				'page' => 2,
				'prevPage' => true,
				'nextPage' => false,
				'pageCount' => 2,
				'options' => [],
				'paramType' => 'querystring'
			]
		];
		$expected = '<link href="/" rel="prev"/>';
		$result = $this->Paginator->meta();
		$this->assertSame($expected, $result);
	}

/**
 * Verify that a page in the middle has both links
 *
 * @return void
 */
	public function testMetaPage10Last() {
		$this->Paginator->request['paging'] = [
			'Article' => [
				'page' => 5,
				'prevPage' => true,
				'nextPage' => true,
				'pageCount' => 10,
				'options' => [],
				'paramType' => 'querystring'
			]
		];
		$expected = '<link href="/?page=4" rel="prev"/>';
		$expected .= '<link href="/?page=6" rel="next"/>';
		$result = $this->Paginator->meta();
		$this->assertSame($expected, $result);
	}

/**
 * Verify that meta() uses URL options
 *
 * @return void
 */
	public function testMetaPageUrlOptions() {
		$this->Paginator->options([
			'url' => ['?' => ['a' => 'b']]
		]);
		$this->Paginator->request['paging'] = [
			'Article' => [
				'page' => 5,
				'prevPage' => true,
				'nextPage' => true,
				'pageCount' => 10,
				'options' => [],
				'paramType' => 'querystring'
			]
		];
		$expected = '<link href="/?a=b&amp;page=4" rel="prev"/>';
		$expected .= '<link href="/?a=b&amp;page=6" rel="next"/>';
		$result = $this->Paginator->meta();
		$this->assertSame($expected, $result);
	}

}
