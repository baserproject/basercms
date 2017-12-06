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
 * @package       Cake.Test.Case.Network
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Dispatcher', 'Routing');
App::uses('Xml', 'Utility');
App::uses('CakeRequest', 'Network');

/**
 * Class TestCakeRequest
 *
 * @package       Cake.Test.Case.Network
 */
class TestCakeRequest extends CakeRequest {

/**
 * reConstruct method
 *
 * @param string $url
 * @param bool $parseEnvironment
 * @return void
 */
	public function reConstruct($url = 'some/path', $parseEnvironment = true) {
		$this->_base();
		if (empty($url)) {
			$url = $this->_url();
		}
		if ($url[0] === '/') {
			$url = substr($url, 1);
		}
		$this->url = $url;

		if ($parseEnvironment) {
			$this->_processPost();
			$this->_processGet();
			$this->_processFiles();
		}
		$this->here = $this->base . '/' . $this->url;
	}

}

/**
 * Class CakeRequestTest
 */
class CakeRequestTest extends CakeTestCase {

/**
 * Setup callback
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_app = Configure::read('App');
		$this->_case = null;
		if (isset($_GET['case'])) {
			$this->_case = $_GET['case'];
			unset($_GET['case']);
		}

		Configure::write('App.baseUrl', false);
	}

/**
 * TearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		if (!empty($this->_case)) {
			$_GET['case'] = $this->_case;
		}
		Configure::write('App', $this->_app);
	}

/**
 * Test the header detector.
 *
 * @return void
 */
	public function testHeaderDetector() {
		$request = new CakeRequest('some/path');
		$request->addDetector('host', ['header' => ['host' => 'cakephp.org']]);

		$_SERVER['HTTP_HOST'] = 'cakephp.org';
		$this->assertTrue($request->is('host'));

		$_SERVER['HTTP_HOST'] = 'php.net';
		$this->assertFalse($request->is('host'));
	}

/**
 * Test the accept header detector.
 *
 * @return void
 */
	public function testExtensionDetector() {
		$request = new CakeRequest('some/path');
		$request->params['ext'] = 'json';
		$this->assertTrue($request->is('json'));

		$request->params['ext'] = 'xml';
		$this->assertFalse($request->is('json'));
	}

/**
 * Test the accept header detector.
 *
 * @return void
 */
	public function testAcceptHeaderDetector() {
		$request = new CakeRequest('some/path');
		$_SERVER['HTTP_ACCEPT'] = 'application/json, text/plain, */*';
		$this->assertTrue($request->is('json'));

		$_SERVER['HTTP_ACCEPT'] = 'text/plain, */*';
		$this->assertFalse($request->is('json'));
	}

/**
 * Test that the autoparse = false constructor works.
 *
 * @return void
 */
	public function testNoAutoParseConstruction() {
		$_GET = [
			'one' => 'param'
		];
		$request = new CakeRequest(null, false);
		$this->assertFalse(isset($request->query['one']));
	}

/**
 * Test the content type method.
 * 
 * @return void
 */
	public function testContentType() {
		$_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
		$request = new CakeRequest('/', false);
		$this->assertEquals('application/json', $request->contentType());

		$_SERVER['CONTENT_TYPE'] = 'application/xml';
		$request = new CakeRequest('/', false);
		$this->assertEquals('application/xml', $request->contentType(), 'prefer non http header.');
	}

/**
 * Test construction
 *
 * @return void
 */
	public function testConstructionGetParsing() {
		$_GET = [
			'one' => 'param',
			'two' => 'banana'
		];
		$request = new CakeRequest('some/path');
		$this->assertEquals($request->query, $_GET);

		$_GET = [
			'one' => 'param',
			'two' => 'banana',
		];
		$request = new CakeRequest('some/path');
		$this->assertEquals($request->query, $_GET);
		$this->assertEquals('some/path', $request->url);
	}

/**
 * Test that querystring args provided in the URL string are parsed.
 *
 * @return void
 */
	public function testQueryStringParsingFromInputUrl() {
		$_GET = [];
		$request = new CakeRequest('some/path?one=something&two=else');
		$expected = ['one' => 'something', 'two' => 'else'];
		$this->assertEquals($expected, $request->query);
		$this->assertEquals('some/path?one=something&two=else', $request->url);
	}

/**
 * Test that named arguments + querystrings are handled correctly.
 *
 * @return void
 */
	public function testQueryStringAndNamedParams() {
		$_SERVER['REQUEST_URI'] = '/tasks/index/page:1?ts=123456';
		$request = new CakeRequest();
		$this->assertEquals('tasks/index/page:1', $request->url);

		$_SERVER['REQUEST_URI'] = '/tasks/index/page:1/?ts=123456';
		$request = new CakeRequest();
		$this->assertEquals('tasks/index/page:1/', $request->url);

		$_SERVER['REQUEST_URI'] = '/some/path?url=http://cakephp.org';
		$request = new CakeRequest();
		$this->assertEquals('some/path', $request->url);

		$_SERVER['REQUEST_URI'] = Configure::read('App.fullBaseUrl') . '/other/path?url=http://cakephp.org';
		$request = new CakeRequest();
		$this->assertEquals('other/path', $request->url);
	}

/**
 * Test addParams() method
 *
 * @return void
 */
	public function testAddParams() {
		$request = new CakeRequest('some/path');
		$request->params = ['controller' => 'posts', 'action' => 'view'];
		$result = $request->addParams(['plugin' => null, 'action' => 'index']);

		$this->assertSame($result, $request, 'Method did not return itself. %s');

		$this->assertEquals('posts', $request->controller);
		$this->assertEquals('index', $request->action);
		$this->assertEquals(null, $request->plugin);
	}

/**
 * Test splicing in paths.
 *
 * @return void
 */
	public function testAddPaths() {
		$request = new CakeRequest('some/path');
		$request->webroot = '/some/path/going/here/';
		$result = $request->addPaths([
			'random' => '/something', 'webroot' => '/', 'here' => '/', 'base' => '/base_dir'
		]);

		$this->assertSame($result, $request, 'Method did not return itself. %s');

		$this->assertEquals('/', $request->webroot);
		$this->assertEquals('/base_dir', $request->base);
		$this->assertEquals('/', $request->here);
		$this->assertFalse(isset($request->random));
	}

/**
 * Test parsing POST data into the object.
 *
 * @return void
 */
	public function testPostParsing() {
		$_POST = ['data' => [
			'Article' => ['title']
		]];
		$request = new CakeRequest('some/path');
		$this->assertEquals($_POST['data'], $request->data);

		$_POST = ['one' => 1, 'two' => 'three'];
		$request = new CakeRequest('some/path');
		$this->assertEquals($_POST, $request->data);

		$_POST = [
			'data' => [
				'Article' => ['title' => 'Testing'],
			],
			'action' => 'update'
		];
		$request = new CakeRequest('some/path');
		$expected = [
			'Article' => ['title' => 'Testing'],
			'action' => 'update'
		];
		$this->assertEquals($expected, $request->data);

		$_POST = ['data' => [
			'Article' => ['title'],
			'Tag' => ['Tag' => [1, 2]]
		]];
		$request = new CakeRequest('some/path');
		$this->assertEquals($_POST['data'], $request->data);

		$_POST = ['data' => [
			'Article' => ['title' => 'some title'],
			'Tag' => ['Tag' => [1, 2]]
		]];
		$request = new CakeRequest('some/path');
		$this->assertEquals($_POST['data'], $request->data);

		$_POST = [
			'a' => [1, 2],
			'b' => [1, 2]
		];
		$request = new CakeRequest('some/path');
		$this->assertEquals($_POST, $request->data);
	}

/**
 * Test parsing PUT data into the object.
 *
 * @return void
 */
	public function testPutParsing() {
		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded; charset=UTF-8';

		$data = ['data' => [
			'Article' => ['title']
		]];

		$request = $this->getMock('TestCakeRequest', ['_readInput']);
		$request->expects($this->at(0))->method('_readInput')
			->will($this->returnValue('data[Article][]=title'));
		$request->reConstruct();
		$this->assertEquals($data['data'], $request->data);

		$data = ['one' => 1, 'two' => 'three'];
		$request = $this->getMock('TestCakeRequest', ['_readInput']);
		$request->expects($this->at(0))->method('_readInput')
			->will($this->returnValue('one=1&two=three'));
		$request->reConstruct();
		$this->assertEquals($data, $request->data);

		$request = $this->getMock('TestCakeRequest', ['_readInput']);
		$request->expects($this->at(0))->method('_readInput')
			->will($this->returnValue('data[Article][title]=Testing&action=update'));
		$request->reConstruct();
		$expected = [
			'Article' => ['title' => 'Testing'],
			'action' => 'update'
		];
		$this->assertEquals($expected, $request->data);

		$_SERVER['REQUEST_METHOD'] = 'DELETE';
		$data = ['data' => [
			'Article' => ['title'],
			'Tag' => ['Tag' => [1, 2]]
		]];
		$request = $this->getMock('TestCakeRequest', ['_readInput']);
		$request->expects($this->at(0))->method('_readInput')
			->will($this->returnValue('data[Article][]=title&Tag[Tag][]=1&Tag[Tag][]=2'));
		$request->reConstruct();
		$this->assertEquals($data['data'], $request->data);

		$data = ['data' => [
			'Article' => ['title' => 'some title'],
			'Tag' => ['Tag' => [1, 2]]
		]];
		$request = $this->getMock('TestCakeRequest', ['_readInput']);
		$request->expects($this->at(0))->method('_readInput')
			->will($this->returnValue('data[Article][title]=some%20title&Tag[Tag][]=1&Tag[Tag][]=2'));
		$request->reConstruct();
		$this->assertEquals($data['data'], $request->data);

		$data = [
			'a' => [1, 2],
			'b' => [1, 2]
		];
		$request = $this->getMock('TestCakeRequest', ['_readInput']);
		$request->expects($this->at(0))->method('_readInput')
			->will($this->returnValue('a[]=1&a[]=2&b[]=1&b[]=2'));
		$request->reConstruct();
		$this->assertEquals($data, $request->data);
	}

/**
 * Test parsing json PUT data into the object.
 *
 * @return void
 */
	public function testPutParsingJSON() {
		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$_SERVER['CONTENT_TYPE'] = 'application/json';

		$request = $this->getMock('TestCakeRequest', ['_readInput']);
		$request->expects($this->at(0))->method('_readInput')
			->will($this->returnValue('{"Article":["title"]}'));
		$request->reConstruct();
		$result = $request->input('json_decode', true);
		$this->assertEquals(['title'], $result['Article']);
	}

/**
 * Test parsing of FILES array
 *
 * @return void
 */
	public function testFilesParsing() {
		$_FILES = [
			'data' => [
				'name' => [
					'File' => [
							['data' => 'cake_sqlserver_patch.patch'],
							['data' => 'controller.diff'],
							['data' => ''],
							['data' => ''],
						],
						'Post' => ['attachment' => 'jquery-1.2.1.js'],
					],
				'type' => [
					'File' => [
						['data' => ''],
						['data' => ''],
						['data' => ''],
						['data' => ''],
					],
					'Post' => ['attachment' => 'application/x-javascript'],
				],
				'tmp_name' => [
					'File' => [
						['data' => '/private/var/tmp/phpy05Ywj'],
						['data' => '/private/var/tmp/php7MBztY'],
						['data' => ''],
						['data' => ''],
					],
					'Post' => ['attachment' => '/private/var/tmp/phpEwlrIo'],
				],
				'error' => [
					'File' => [
						['data' => 0],
						['data' => 0],
						['data' => 4],
						['data' => 4]
					],
					'Post' => ['attachment' => 0]
				],
				'size' => [
					'File' => [
						['data' => 6271],
						['data' => 350],
						['data' => 0],
						['data' => 0],
					],
					'Post' => ['attachment' => 80469]
				],
			]
		];

		$request = new CakeRequest('some/path');
		$expected = [
			'File' => [
				[
					'data' => [
						'name' => 'cake_sqlserver_patch.patch',
						'type' => '',
						'tmp_name' => '/private/var/tmp/phpy05Ywj',
						'error' => 0,
						'size' => 6271,
					]
				],
				[
					'data' => [
						'name' => 'controller.diff',
						'type' => '',
						'tmp_name' => '/private/var/tmp/php7MBztY',
						'error' => 0,
						'size' => 350,
					]
				],
				[
					'data' => [
						'name' => '',
						'type' => '',
						'tmp_name' => '',
						'error' => 4,
						'size' => 0,
					]
				],
				[
					'data' => [
						'name' => '',
						'type' => '',
						'tmp_name' => '',
						'error' => 4,
						'size' => 0,
					]
				],
			],
			'Post' => [
				'attachment' => [
					'name' => 'jquery-1.2.1.js',
					'type' => 'application/x-javascript',
					'tmp_name' => '/private/var/tmp/phpEwlrIo',
					'error' => 0,
					'size' => 80469,
				]
			]
		];
		$this->assertEquals($expected, $request->data);

		$_FILES = [
			'data' => [
				'name' => [
					'Document' => [
						1 => [
							'birth_cert' => 'born on.txt',
							'passport' => 'passport.txt',
							'drivers_license' => 'ugly pic.jpg'
						],
						2 => [
							'birth_cert' => 'aunt betty.txt',
							'passport' => 'betty-passport.txt',
							'drivers_license' => 'betty-photo.jpg'
						],
					],
				],
				'type' => [
					'Document' => [
						1 => [
							'birth_cert' => 'application/octet-stream',
							'passport' => 'application/octet-stream',
							'drivers_license' => 'application/octet-stream',
						],
						2 => [
							'birth_cert' => 'application/octet-stream',
							'passport' => 'application/octet-stream',
							'drivers_license' => 'application/octet-stream',
						]
					]
				],
				'tmp_name' => [
					'Document' => [
						1 => [
							'birth_cert' => '/private/var/tmp/phpbsUWfH',
							'passport' => '/private/var/tmp/php7f5zLt',
							'drivers_license' => '/private/var/tmp/phpMXpZgT',
						],
						2 => [
							'birth_cert' => '/private/var/tmp/php5kHZt0',
							'passport' => '/private/var/tmp/phpnYkOuM',
							'drivers_license' => '/private/var/tmp/php9Rq0P3',
						]
					]
				],
				'error' => [
					'Document' => [
						1 => [
							'birth_cert' => 0,
							'passport' => 0,
							'drivers_license' => 0,
						],
						2 => [
							'birth_cert' => 0,
							'passport' => 0,
							'drivers_license' => 0,
						]
					]
				],
				'size' => [
					'Document' => [
						1 => [
							'birth_cert' => 123,
							'passport' => 458,
							'drivers_license' => 875,
						],
						2 => [
							'birth_cert' => 876,
							'passport' => 976,
							'drivers_license' => 9783,
						]
					]
				]
			]
		];

		$request = new CakeRequest('some/path');
		$expected = [
			'Document' => [
				1 => [
					'birth_cert' => [
						'name' => 'born on.txt',
						'tmp_name' => '/private/var/tmp/phpbsUWfH',
						'error' => 0,
						'size' => 123,
						'type' => 'application/octet-stream',
					],
					'passport' => [
						'name' => 'passport.txt',
						'tmp_name' => '/private/var/tmp/php7f5zLt',
						'error' => 0,
						'size' => 458,
						'type' => 'application/octet-stream',
					],
					'drivers_license' => [
						'name' => 'ugly pic.jpg',
						'tmp_name' => '/private/var/tmp/phpMXpZgT',
						'error' => 0,
						'size' => 875,
						'type' => 'application/octet-stream',
					],
				],
				2 => [
					'birth_cert' => [
						'name' => 'aunt betty.txt',
						'tmp_name' => '/private/var/tmp/php5kHZt0',
						'error' => 0,
						'size' => 876,
						'type' => 'application/octet-stream',
					],
					'passport' => [
						'name' => 'betty-passport.txt',
						'tmp_name' => '/private/var/tmp/phpnYkOuM',
						'error' => 0,
						'size' => 976,
						'type' => 'application/octet-stream',
					],
					'drivers_license' => [
						'name' => 'betty-photo.jpg',
						'tmp_name' => '/private/var/tmp/php9Rq0P3',
						'error' => 0,
						'size' => 9783,
						'type' => 'application/octet-stream',
					],
				],
			]
		];
		$this->assertEquals($expected, $request->data);

		$_FILES = [
			'data' => [
				'name' => ['birth_cert' => 'born on.txt'],
				'type' => ['birth_cert' => 'application/octet-stream'],
				'tmp_name' => ['birth_cert' => '/private/var/tmp/phpbsUWfH'],
				'error' => ['birth_cert' => 0],
				'size' => ['birth_cert' => 123]
			]
		];

		$request = new CakeRequest('some/path');
		$expected = [
			'birth_cert' => [
				'name' => 'born on.txt',
				'type' => 'application/octet-stream',
				'tmp_name' => '/private/var/tmp/phpbsUWfH',
				'error' => 0,
				'size' => 123
			]
		];
		$this->assertEquals($expected, $request->data);

		$_FILES = [
			'something' => [
				'name' => 'something.txt',
				'type' => 'text/plain',
				'tmp_name' => '/some/file',
				'error' => 0,
				'size' => 123
			]
		];
		$request = new CakeRequest('some/path');
		$this->assertEquals($request->params['form'], $_FILES);
	}

/**
 * Test that files in the 0th index work.
 *
 * @return void
 */
	public function testFilesZeroithIndex() {
		$_FILES = [
			0 => [
				'name' => 'cake_sqlserver_patch.patch',
				'type' => 'text/plain',
				'tmp_name' => '/private/var/tmp/phpy05Ywj',
				'error' => 0,
				'size' => 6271,
			],
		];

		$request = new CakeRequest('some/path');
		$this->assertEquals($_FILES, $request->params['form']);
	}

/**
 * Test method overrides coming in from POST data.
 *
 * @return void
 */
	public function testMethodOverrides() {
		$_POST = ['_method' => 'POST'];
		$request = new CakeRequest('some/path');
		$this->assertEquals(env('REQUEST_METHOD'), 'POST');

		$_POST = ['_method' => 'DELETE'];
		$request = new CakeRequest('some/path');
		$this->assertEquals(env('REQUEST_METHOD'), 'DELETE');

		$_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'PUT';
		$request = new CakeRequest('some/path');
		$this->assertEquals(env('REQUEST_METHOD'), 'PUT');
	}

/**
 * Test the clientIp method.
 *
 * @return void
 */
	public function testclientIp() {
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.5, 10.0.1.1, proxy.com';
		$_SERVER['HTTP_CLIENT_IP'] = '192.168.1.2';
		$_SERVER['REMOTE_ADDR'] = '192.168.1.3';

		$request = new CakeRequest('some/path');
		$this->assertEquals('192.168.1.3', $request->clientIp(), 'Use remote_addr in safe mode');
		$this->assertEquals('192.168.1.5', $request->clientIp(false), 'Use x-forwarded');

		unset($_SERVER['HTTP_X_FORWARDED_FOR']);
		$this->assertEquals('192.168.1.3', $request->clientIp(), 'safe uses remote_addr');
		$this->assertEquals('192.168.1.2', $request->clientIp(false), 'unsafe reads from client_ip');

		unset($_SERVER['HTTP_CLIENT_IP']);
		$this->assertEquals('192.168.1.3', $request->clientIp(), 'use remote_addr');
		$this->assertEquals('192.168.1.3', $request->clientIp(false), 'use remote_addr');
	}

/**
 * Test the referrer function.
 *
 * @return void
 */
	public function testReferer() {
		$request = new CakeRequest('some/path');
		$request->webroot = '/';

		$_SERVER['HTTP_REFERER'] = 'http://cakephp.org';
		$result = $request->referer();
		$this->assertSame($result, 'http://cakephp.org');

		$_SERVER['HTTP_REFERER'] = '';
		$result = $request->referer();
		$this->assertSame($result, '/');

		$_SERVER['HTTP_REFERER'] = Configure::read('App.fullBaseUrl') . '/';
		$result = $request->referer(true);
		$this->assertSame($result, '/');

		$_SERVER['HTTP_REFERER'] = Configure::read('App.fullBaseUrl') . '/some/path';
		$result = $request->referer(true);
		$this->assertSame($result, '/some/path');

		$_SERVER['HTTP_REFERER'] = Configure::read('App.fullBaseUrl') . '/some/path';
		$result = $request->referer(false);
		$this->assertSame($result, Configure::read('App.fullBaseUrl') . '/some/path');

		$_SERVER['HTTP_REFERER'] = Configure::read('App.fullBaseUrl') . '/recipes/add';
		$result = $request->referer(true);
		$this->assertSame($result, '/recipes/add');
	}

/**
 * Test referer() with a base path that duplicates the
 * first segment.
 *
 * @return void
 */
	public function testRefererBasePath() {
		$request = new CakeRequest('some/path');
		$request->url = 'users/login';
		$request->webroot = '/waves/';
		$request->base = '/waves';
		$request->here = '/waves/users/login';

		$_SERVER['HTTP_REFERER'] = FULL_BASE_URL . '/waves/waves/add';

		$result = $request->referer(true);
		$this->assertSame($result, '/waves/add');
	}

/**
 * test the simple uses of is()
 *
 * @return void
 */
	public function testIsHttpMethods() {
		$request = new CakeRequest('some/path');

		$this->assertFalse($request->is('undefined-behavior'));

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->assertTrue($request->is('get'));

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->assertTrue($request->is('POST'));

		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$this->assertTrue($request->is('put'));
		$this->assertFalse($request->is('get'));

		$_SERVER['REQUEST_METHOD'] = 'DELETE';
		$this->assertTrue($request->is('delete'));
		$this->assertTrue($request->isDelete());

		$_SERVER['REQUEST_METHOD'] = 'delete';
		$this->assertFalse($request->is('delete'));
	}

/**
 * Test is() with json and xml.
 *
 * @return void
 */
	public function testIsJsonAndXml() {
		$request = new CakeRequest('some/path');

		$_SERVER['HTTP_ACCEPT'] = 'application/json, text/plain, */*';
		$this->assertTrue($request->is(['json']));

		$_SERVER['HTTP_ACCEPT'] = 'application/xml, text/plain, */*';
		$this->assertTrue($request->is(['xml']));

		$_SERVER['HTTP_ACCEPT'] = 'text/xml, */*';
		$this->assertTrue($request->is(['xml']));
	}

/**
 * Test is() with multiple types.
 *
 * @return void
 */
	public function testIsMultiple() {
		$request = new CakeRequest('some/path');

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->assertTrue($request->is(['get', 'post']));

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->assertTrue($request->is(['get', 'post']));

		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$this->assertFalse($request->is(['get', 'post']));
	}

/**
 * Test isAll()
 *
 * @return void
 */
	public function testIsAll() {
		$request = new CakeRequest('some/path');

		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$this->assertTrue($request->isAll(['ajax', 'get']));
		$this->assertFalse($request->isAll(['post', 'get']));
		$this->assertFalse($request->isAll(['ajax', 'post']));
	}

/**
 * Test the method() method.
 *
 * @return void
 */
	public function testMethod() {
		$_SERVER['REQUEST_METHOD'] = 'delete';
		$request = new CakeRequest('some/path');

		$this->assertEquals('delete', $request->method());
	}

/**
 * Test host retrieval.
 *
 * @return void
 */
	public function testHost() {
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['HTTP_X_FORWARDED_HOST'] = 'cakephp.org';
		$request = new CakeRequest('some/path');

		$this->assertEquals('localhost', $request->host());
		$this->assertEquals('cakephp.org', $request->host(true));
	}

/**
 * Test domain retrieval.
 *
 * @return void
 */
	public function testDomain() {
		$_SERVER['HTTP_HOST'] = 'something.example.com';
		$request = new CakeRequest('some/path');

		$this->assertEquals('example.com', $request->domain());

		$_SERVER['HTTP_HOST'] = 'something.example.co.uk';
		$this->assertEquals('example.co.uk', $request->domain(2));
	}

/**
 * Test getting subdomains for a host.
 *
 * @return void
 */
	public function testSubdomain() {
		$_SERVER['HTTP_HOST'] = 'something.example.com';
		$request = new CakeRequest('some/path');

		$this->assertEquals(['something'], $request->subdomains());

		$_SERVER['HTTP_HOST'] = 'www.something.example.com';
		$this->assertEquals(['www', 'something'], $request->subdomains());

		$_SERVER['HTTP_HOST'] = 'www.something.example.co.uk';
		$this->assertEquals(['www', 'something'], $request->subdomains(2));

		$_SERVER['HTTP_HOST'] = 'example.co.uk';
		$this->assertEquals([], $request->subdomains(2));
	}

/**
 * Test ajax, flash and friends
 *
 * @return void
 */
	public function testisAjaxFlashAndFriends() {
		$request = new CakeRequest('some/path');

		$_SERVER['HTTP_USER_AGENT'] = 'Shockwave Flash';
		$this->assertTrue($request->is('flash'));

		$_SERVER['HTTP_USER_AGENT'] = 'Adobe Flash';
		$this->assertTrue($request->is('flash'));

		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		$this->assertTrue($request->is('ajax'));

		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHTTPREQUEST';
		$this->assertFalse($request->is('ajax'));
		$this->assertFalse($request->isAjax());

		$_SERVER['HTTP_USER_AGENT'] = 'Android 2.0';
		$this->assertTrue($request->is('mobile'));
		$this->assertTrue($request->isMobile());

		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 5.1; rv:2.0b6pre) Gecko/20100902 Firefox/4.0b6pre Fennec/2.0b1pre';
		$this->assertTrue($request->is('mobile'));
		$this->assertTrue($request->isMobile());

		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0; SAMSUNG; OMNIA7)';
		$this->assertTrue($request->is('mobile'));
		$this->assertTrue($request->isMobile());
	}

/**
 * Test __call exceptions
 *
 * @expectedException CakeException
 * @return void
 */
	public function testMagicCallExceptionOnUnknownMethod() {
		$request = new CakeRequest('some/path');
		$request->IamABanana();
	}

/**
 * Test is(ssl)
 *
 * @return void
 */
	public function testIsSsl() {
		$request = new CakeRequest('some/path');

		$_SERVER['HTTPS'] = 1;
		$this->assertTrue($request->is('ssl'));

		$_SERVER['HTTPS'] = 'on';
		$this->assertTrue($request->is('ssl'));

		$_SERVER['HTTPS'] = '1';
		$this->assertTrue($request->is('ssl'));

		$_SERVER['HTTPS'] = 'I am not empty';
		$this->assertTrue($request->is('ssl'));

		$_SERVER['HTTPS'] = 1;
		$this->assertTrue($request->is('ssl'));

		$_SERVER['HTTPS'] = 'off';
		$this->assertFalse($request->is('ssl'));

		$_SERVER['HTTPS'] = false;
		$this->assertFalse($request->is('ssl'));

		$_SERVER['HTTPS'] = '';
		$this->assertFalse($request->is('ssl'));
	}

/**
 * Test getting request params with object properties.
 *
 * @return void
 */
	public function testMagicget() {
		$request = new CakeRequest('some/path');
		$request->params = ['controller' => 'posts', 'action' => 'view', 'plugin' => 'blogs'];

		$this->assertEquals('posts', $request->controller);
		$this->assertEquals('view', $request->action);
		$this->assertEquals('blogs', $request->plugin);
		$this->assertNull($request->banana);
	}

/**
 * Test isset()/empty() with overloaded properties.
 *
 * @return void
 */
	public function testMagicisset() {
		$request = new CakeRequest('some/path');
		$request->params = [
			'controller' => 'posts',
			'action' => 'view',
			'plugin' => 'blogs',
			'named' => []
		];

		$this->assertTrue(isset($request->controller));
		$this->assertFalse(isset($request->notthere));
		$this->assertFalse(empty($request->controller));
		$this->assertTrue(empty($request->named));
	}

/**
 * Test the array access implementation
 *
 * @return void
 */
	public function testArrayAccess() {
		$request = new CakeRequest('some/path');
		$request->params = ['controller' => 'posts', 'action' => 'view', 'plugin' => 'blogs'];

		$this->assertEquals('posts', $request['controller']);

		$request['slug'] = 'speedy-slug';
		$this->assertEquals('speedy-slug', $request->slug);
		$this->assertEquals('speedy-slug', $request['slug']);

		$this->assertTrue(isset($request['action']));
		$this->assertFalse(isset($request['wrong-param']));

		$this->assertTrue(isset($request['plugin']));
		unset($request['plugin']);
		$this->assertFalse(isset($request['plugin']));
		$this->assertNull($request['plugin']);
		$this->assertNull($request->plugin);

		$request = new CakeRequest('some/path?one=something&two=else');
		$this->assertTrue(isset($request['url']['one']));

		$request->data = ['Post' => ['title' => 'something']];
		$this->assertEquals('something', $request['data']['Post']['title']);
	}

/**
 * Test adding detectors and having them work.
 *
 * @return void
 */
	public function testAddDetector() {
		$request = new CakeRequest('some/path');
		$request->addDetector('compare', ['env' => 'TEST_VAR', 'value' => 'something']);

		$_SERVER['TEST_VAR'] = 'something';
		$this->assertTrue($request->is('compare'), 'Value match failed.');

		$_SERVER['TEST_VAR'] = 'wrong';
		$this->assertFalse($request->is('compare'), 'Value mis-match failed.');

		$request->addDetector('compareCamelCase', ['env' => 'TEST_VAR', 'value' => 'foo']);

		$_SERVER['TEST_VAR'] = 'foo';
		$this->assertTrue($request->is('compareCamelCase'), 'Value match failed.');
		$this->assertTrue($request->is('comparecamelcase'), 'detectors should be case insensitive');
		$this->assertTrue($request->is('COMPARECAMELCASE'), 'detectors should be case insensitive');

		$_SERVER['TEST_VAR'] = 'not foo';
		$this->assertFalse($request->is('compareCamelCase'), 'Value match failed.');
		$this->assertFalse($request->is('comparecamelcase'), 'detectors should be case insensitive');
		$this->assertFalse($request->is('COMPARECAMELCASE'), 'detectors should be case insensitive');

		$request->addDetector('banana', ['env' => 'TEST_VAR', 'pattern' => '/^ban.*$/']);
		$_SERVER['TEST_VAR'] = 'banana';
		$this->assertTrue($request->isBanana());

		$_SERVER['TEST_VAR'] = 'wrong value';
		$this->assertFalse($request->isBanana());

		$request->addDetector('mobile', ['options' => ['Imagination']]);
		$_SERVER['HTTP_USER_AGENT'] = 'Imagination land';
		$this->assertTrue($request->isMobile());

		$_SERVER['HTTP_USER_AGENT'] = 'iPhone 3.0';
		$this->assertTrue($request->isMobile());

		$request->addDetector('callme', ['env' => 'TEST_VAR', 'callback' => [$this, 'detectCallback']]);

		$request->addDetector('index', ['param' => 'action', 'value' => 'index']);
		$request->params['action'] = 'index';
		$this->assertTrue($request->isIndex());

		$request->params['action'] = 'add';
		$this->assertFalse($request->isIndex());

		$request->return = true;
		$this->assertTrue($request->isCallMe());

		$request->return = false;
		$this->assertFalse($request->isCallMe());

		$request->addDetector('extension', ['param' => 'ext', 'options' => ['pdf', 'png', 'txt']]);
		$request->params['ext'] = 'pdf';
		$this->assertTrue($request->is('extension'));

		$request->params['ext'] = 'exe';
		$this->assertFalse($request->isExtension());
	}

/**
 * Helper function for testing callbacks.
 *
 * @param $request
 * @return bool
 */
	public function detectCallback($request) {
		return (bool)$request->return;
	}

/**
 * Test getting headers
 *
 * @return void
 */
	public function testHeader() {
		$_SERVER['HTTP_X_THING'] = '';
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_4; en-ca) AppleWebKit/534.8+ (KHTML, like Gecko) Version/5.0 Safari/533.16';
		$request = new CakeRequest('/', false);

		$this->assertEquals($_SERVER['HTTP_HOST'], $request->header('host'));
		$this->assertEquals($_SERVER['HTTP_USER_AGENT'], $request->header('User-Agent'));
		$this->assertSame('', $request->header('X-thing'));
	}

/**
 * Test accepts() with and without parameters
 *
 * @return void
 */
	public function testAccepts() {
		$_SERVER['HTTP_ACCEPT'] = 'text/xml,application/xml;q=0.9,application/xhtml+xml,text/html,text/plain,image/png';
		$request = new CakeRequest('/', false);

		$result = $request->accepts();
		$expected = [
			'text/xml', 'application/xhtml+xml', 'text/html', 'text/plain', 'image/png', 'application/xml'
		];
		$this->assertEquals($expected, $result, 'Content types differ.');

		$result = $request->accepts('text/html');
		$this->assertTrue($result);

		$result = $request->accepts('image/gif');
		$this->assertFalse($result);
	}

/**
 * Test that accept header types are trimmed for comparisons.
 *
 * @return void
 */
	public function testAcceptWithWhitespace() {
		$_SERVER['HTTP_ACCEPT'] = 'text/xml  ,  text/html ,  text/plain,image/png';
		$request = new CakeRequest('/', false);
		$result = $request->accepts();
		$expected = [
			'text/xml', 'text/html', 'text/plain', 'image/png'
		];
		$this->assertEquals($expected, $result, 'Content types differ.');

		$this->assertTrue($request->accepts('text/html'));
	}

/**
 * Content types from accepts() should respect the client's q preference values.
 *
 * @return void
 */
	public function testAcceptWithQvalueSorting() {
		$_SERVER['HTTP_ACCEPT'] = 'text/html;q=0.8,application/json;q=0.7,application/xml;q=1.0';
		$request = new CakeRequest('/', false);
		$result = $request->accepts();
		$expected = ['application/xml', 'text/html', 'application/json'];
		$this->assertEquals($expected, $result);
	}

/**
 * Test the raw parsing of accept headers into the q value formatting.
 *
 * @return void
 */
	public function testParseAcceptWithQValue() {
		$_SERVER['HTTP_ACCEPT'] = 'text/html;q=0.8,application/json;q=0.7,application/xml;q=1.0,image/png';
		$request = new CakeRequest('/', false);
		$result = $request->parseAccept();
		$expected = [
			'1.0' => ['application/xml', 'image/png'],
			'0.8' => ['text/html'],
			'0.7' => ['application/json'],
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Test parsing accept with a confusing accept value.
 *
 * @return void
 */
	public function testParseAcceptNoQValues() {
		$_SERVER['HTTP_ACCEPT'] = 'application/json, text/plain, */*';

		$request = new CakeRequest('/', false);
		$result = $request->parseAccept();
		$expected = [
			'1.0' => ['application/json', 'text/plain', '*/*'],
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Test parsing accept ignores index param
 *
 * @return void
 */
	public function testParseAcceptIgnoreAcceptExtensions() {
		$_SERVER['HTTP_ACCEPT'] = 'application/json;level=1, text/plain, */*';

		$request = new CakeRequest('/', false);
		$result = $request->parseAccept();
		$expected = [
			'1.0' => ['application/json', 'text/plain', '*/*'],
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Test that parsing accept headers with invalid syntax works.
 *
 * The header used is missing a q value for application/xml.
 *
 * @return void
 */
	public function testParseAcceptInvalidSyntax() {
		$_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;image/png,image/jpeg,image/*;q=0.9,*/*;q=0.8';
		$request = new CakeRequest('/', false);
		$result = $request->parseAccept();
		$expected = [
			'1.0' => ['text/html', 'application/xhtml+xml', 'application/xml', 'image/jpeg'],
			'0.9' => ['image/*'],
			'0.8' => ['*/*'],
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Test baseUrl and webroot with ModRewrite
 *
 * @return void
 */
	public function testBaseUrlAndWebrootWithModRewrite() {
		Configure::write('App.baseUrl', false);

		$_SERVER['DOCUMENT_ROOT'] = '/cake/repo/branches';
		$_SERVER['PHP_SELF'] = '/urlencode me/app/webroot/index.php';
		$_SERVER['PATH_INFO'] = '/posts/view/1';

		$request = new CakeRequest();
		$this->assertEquals('/urlencode%20me', $request->base);
		$this->assertEquals('/urlencode%20me/', $request->webroot);
		$this->assertEquals('posts/view/1', $request->url);

		$_SERVER['DOCUMENT_ROOT'] = '/cake/repo/branches';
		$_SERVER['PHP_SELF'] = '/1.2.x.x/app/webroot/index.php';
		$_SERVER['PATH_INFO'] = '/posts/view/1';

		$request = new CakeRequest();
		$this->assertEquals('/1.2.x.x', $request->base);
		$this->assertEquals('/1.2.x.x/', $request->webroot);
		$this->assertEquals('posts/view/1', $request->url);

		$_SERVER['DOCUMENT_ROOT'] = '/cake/repo/branches/1.2.x.x/app/webroot';
		$_SERVER['PHP_SELF'] = '/index.php';
		$_SERVER['PATH_INFO'] = '/posts/add';
		$request = new CakeRequest();

		$this->assertEquals('', $request->base);
		$this->assertEquals('/', $request->webroot);
		$this->assertEquals('posts/add', $request->url);

		$_SERVER['DOCUMENT_ROOT'] = '/cake/repo/branches/1.2.x.x/test/';
		$_SERVER['PHP_SELF'] = '/webroot/index.php';
		$request = new CakeRequest();

		$this->assertEquals('', $request->base);
		$this->assertEquals('/', $request->webroot);

		$_SERVER['DOCUMENT_ROOT'] = '/some/apps/where';
		$_SERVER['PHP_SELF'] = '/app/webroot/index.php';
		$request = new CakeRequest();

		$this->assertEquals('', $request->base);
		$this->assertEquals('/', $request->webroot);

		Configure::write('App.dir', 'auth');

		$_SERVER['DOCUMENT_ROOT'] = '/cake/repo/branches';
		$_SERVER['PHP_SELF'] = '/demos/auth/webroot/index.php';

		$request = new CakeRequest();

		$this->assertEquals('/demos/auth', $request->base);
		$this->assertEquals('/demos/auth/', $request->webroot);

		Configure::write('App.dir', 'code');

		$_SERVER['DOCUMENT_ROOT'] = '/Library/WebServer/Documents';
		$_SERVER['PHP_SELF'] = '/clients/PewterReport/code/webroot/index.php';
		$request = new CakeRequest();

		$this->assertEquals('/clients/PewterReport/code', $request->base);
		$this->assertEquals('/clients/PewterReport/code/', $request->webroot);
	}

/**
 * Test baseUrl with ModRewrite alias
 *
 * @return void
 */
	public function testBaseUrlwithModRewriteAlias() {
		$_SERVER['DOCUMENT_ROOT'] = '/home/aplusnur/public_html';
		$_SERVER['PHP_SELF'] = '/control/index.php';

		Configure::write('App.base', '/control');

		$request = new CakeRequest();

		$this->assertEquals('/control', $request->base);
		$this->assertEquals('/control/', $request->webroot);

		Configure::write('App.base', false);
		Configure::write('App.dir', 'affiliate');
		Configure::write('App.webroot', 'newaffiliate');

		$_SERVER['DOCUMENT_ROOT'] = '/var/www/abtravaff/html';
		$_SERVER['PHP_SELF'] = '/newaffiliate/index.php';
		$request = new CakeRequest();

		$this->assertEquals('/newaffiliate', $request->base);
		$this->assertEquals('/newaffiliate/', $request->webroot);
	}

/**
 * Test base, webroot, URL and here parsing when there is URL rewriting but
 * CakePHP gets called with index.php in URL nonetheless.
 *
 * Tests uri with
 * - index.php/
 * - index.php/
 * - index.php/apples/
 * - index.php/bananas/eat/tasty_banana
 *
 * @return void
 */
	public function testBaseUrlWithModRewriteAndIndexPhp() {
		$_SERVER['REQUEST_URI'] = '/cakephp/app/webroot/index.php';
		$_SERVER['PHP_SELF'] = '/cakephp/app/webroot/index.php';
		unset($_SERVER['PATH_INFO']);
		$request = new CakeRequest();

		$this->assertEquals('/cakephp', $request->base);
		$this->assertEquals('/cakephp/', $request->webroot);
		$this->assertEquals('', $request->url);
		$this->assertEquals('/cakephp/', $request->here);

		$_SERVER['REQUEST_URI'] = '/cakephp/app/webroot/index.php/';
		$_SERVER['PHP_SELF'] = '/cakephp/app/webroot/index.php/';
		$_SERVER['PATH_INFO'] = '/';
		$request = new CakeRequest();

		$this->assertEquals('/cakephp', $request->base);
		$this->assertEquals('/cakephp/', $request->webroot);
		$this->assertEquals('', $request->url);
		$this->assertEquals('/cakephp/', $request->here);

		$_SERVER['REQUEST_URI'] = '/cakephp/app/webroot/index.php/apples';
		$_SERVER['PHP_SELF'] = '/cakephp/app/webroot/index.php/apples';
		$_SERVER['PATH_INFO'] = '/apples';
		$request = new CakeRequest();

		$this->assertEquals('/cakephp', $request->base);
		$this->assertEquals('/cakephp/', $request->webroot);
		$this->assertEquals('apples', $request->url);
		$this->assertEquals('/cakephp/apples', $request->here);

		$_SERVER['REQUEST_URI'] = '/cakephp/app/webroot/index.php/melons/share/';
		$_SERVER['PHP_SELF'] = '/cakephp/app/webroot/index.php/melons/share/';
		$_SERVER['PATH_INFO'] = '/melons/share/';
		$request = new CakeRequest();

		$this->assertEquals('/cakephp', $request->base);
		$this->assertEquals('/cakephp/', $request->webroot);
		$this->assertEquals('melons/share/', $request->url);
		$this->assertEquals('/cakephp/melons/share/', $request->here);

		$_SERVER['REQUEST_URI'] = '/cakephp/app/webroot/index.php/bananas/eat/tasty_banana';
		$_SERVER['PHP_SELF'] = '/cakephp/app/webroot/index.php/bananas/eat/tasty_banana';
		$_SERVER['PATH_INFO'] = '/bananas/eat/tasty_banana';
		$request = new CakeRequest();

		$this->assertEquals('/cakephp', $request->base);
		$this->assertEquals('/cakephp/', $request->webroot);
		$this->assertEquals('bananas/eat/tasty_banana', $request->url);
		$this->assertEquals('/cakephp/bananas/eat/tasty_banana', $request->here);
	}

/**
 * Test that even if mod_rewrite is on, and the url contains index.php
 * and there are numerous //s that the base/webroot is calculated correctly.
 *
 * @return void
 */
	public function testBaseUrlWithModRewriteAndExtraSlashes() {
		$_SERVER['REQUEST_URI'] = '/cakephp/webroot///index.php/bananas/eat';
		$_SERVER['PHP_SELF'] = '/cakephp/webroot///index.php/bananas/eat';
		$_SERVER['PATH_INFO'] = '/bananas/eat';
		$request = new CakeRequest();

		$this->assertEquals('/cakephp', $request->base);
		$this->assertEquals('/cakephp/', $request->webroot);
		$this->assertEquals('bananas/eat', $request->url);
		$this->assertEquals('/cakephp/bananas/eat', $request->here);
	}

/**
 * Test base, webroot, and URL parsing when there is no URL rewriting
 *
 * @return void
 */
	public function testBaseUrlWithNoModRewrite() {
		$_SERVER['DOCUMENT_ROOT'] = '/Users/markstory/Sites';
		$_SERVER['SCRIPT_FILENAME'] = '/Users/markstory/Sites/cake/index.php';
		$_SERVER['PHP_SELF'] = '/cake/index.php/posts/index';
		$_SERVER['REQUEST_URI'] = '/cake/index.php/posts/index';

		Configure::write('App', [
			'dir' => APP_DIR,
			'webroot' => WEBROOT_DIR,
			'base' => false,
			'baseUrl' => '/cake/index.php'
		]);

		$request = new CakeRequest();
		$this->assertEquals('/cake/index.php', $request->base);
		$this->assertEquals('/cake/app/webroot/', $request->webroot);
		$this->assertEquals('posts/index', $request->url);
	}

/**
 * Test baseUrl and webroot with baseUrl
 *
 * @return void
 */
	public function testBaseUrlAndWebrootWithBaseUrl() {
		Configure::write('App.dir', 'app');
		Configure::write('App.baseUrl', '/app/webroot/index.php');

		$request = new CakeRequest();
		$this->assertEquals('/app/webroot/index.php', $request->base);
		$this->assertEquals('/app/webroot/', $request->webroot);

		Configure::write('App.baseUrl', '/app/webroot/test.php');
		$request = new CakeRequest();
		$this->assertEquals('/app/webroot/test.php', $request->base);
		$this->assertEquals('/app/webroot/', $request->webroot);

		Configure::write('App.baseUrl', '/app/index.php');
		$request = new CakeRequest();
		$this->assertEquals('/app/index.php', $request->base);
		$this->assertEquals('/app/webroot/', $request->webroot);

		Configure::write('App.baseUrl', '/CakeBB/app/webroot/index.php');
		$request = new CakeRequest();
		$this->assertEquals('/CakeBB/app/webroot/index.php', $request->base);
		$this->assertEquals('/CakeBB/app/webroot/', $request->webroot);

		Configure::write('App.baseUrl', '/CakeBB/app/index.php');
		$request = new CakeRequest();

		$this->assertEquals('/CakeBB/app/index.php', $request->base);
		$this->assertEquals('/CakeBB/app/webroot/', $request->webroot);

		Configure::write('App.baseUrl', '/CakeBB/index.php');
		$request = new CakeRequest();

		$this->assertEquals('/CakeBB/index.php', $request->base);
		$this->assertEquals('/CakeBB/app/webroot/', $request->webroot);

		Configure::write('App.baseUrl', '/dbhauser/index.php');
		$_SERVER['DOCUMENT_ROOT'] = '/kunden/homepages/4/d181710652/htdocs/joomla';
		$_SERVER['SCRIPT_FILENAME'] = '/kunden/homepages/4/d181710652/htdocs/joomla/dbhauser/index.php';
		$request = new CakeRequest();

		$this->assertEquals('/dbhauser/index.php', $request->base);
		$this->assertEquals('/dbhauser/app/webroot/', $request->webroot);
	}

/**
 * Test baseUrl with no rewrite and using the top level index.php.
 *
 * @return void
 */
	public function testBaseUrlNoRewriteTopLevelIndex() {
		Configure::write('App.baseUrl', '/index.php');
		$_SERVER['DOCUMENT_ROOT'] = '/Users/markstory/Sites/cake_dev';
		$_SERVER['SCRIPT_FILENAME'] = '/Users/markstory/Sites/cake_dev/index.php';

		$request = new CakeRequest();
		$this->assertEquals('/index.php', $request->base);
		$this->assertEquals('/app/webroot/', $request->webroot);
	}

/**
 * Check that a sub-directory containing app|webroot doesn't get mishandled when re-writing is off.
 *
 * @return void
 */
	public function testBaseUrlWithAppAndWebrootInDirname() {
		Configure::write('App.baseUrl', '/approval/index.php');
		$_SERVER['DOCUMENT_ROOT'] = '/Users/markstory/Sites/';
		$_SERVER['SCRIPT_FILENAME'] = '/Users/markstory/Sites/approval/index.php';

		$request = new CakeRequest();
		$this->assertEquals('/approval/index.php', $request->base);
		$this->assertEquals('/approval/app/webroot/', $request->webroot);

		Configure::write('App.baseUrl', '/webrootable/index.php');
		$_SERVER['DOCUMENT_ROOT'] = '/Users/markstory/Sites/';
		$_SERVER['SCRIPT_FILENAME'] = '/Users/markstory/Sites/webrootable/index.php';

		$request = new CakeRequest();
		$this->assertEquals('/webrootable/index.php', $request->base);
		$this->assertEquals('/webrootable/app/webroot/', $request->webroot);
	}

/**
 * Test baseUrl with no rewrite, and using the app/webroot/index.php file as is normal with virtual hosts.
 *
 * @return void
 */
	public function testBaseUrlNoRewriteWebrootIndex() {
		Configure::write('App.baseUrl', '/index.php');
		$_SERVER['DOCUMENT_ROOT'] = '/Users/markstory/Sites/cake_dev/app/webroot';
		$_SERVER['SCRIPT_FILENAME'] = '/Users/markstory/Sites/cake_dev/app/webroot/index.php';

		$request = new CakeRequest();
		$this->assertEquals('/index.php', $request->base);
		$this->assertEquals('/', $request->webroot);
	}

/**
 * Test that a request with a . in the main GET parameter is filtered out.
 * PHP changes GET parameter keys containing dots to _.
 *
 * @return void
 */
	public function testGetParamsWithDot() {
		$_GET = [];
		$_GET['/posts/index/add_add'] = '';
		$_SERVER['PHP_SELF'] = '/app/webroot/index.php';
		$_SERVER['REQUEST_URI'] = '/posts/index/add.add';
		$request = new CakeRequest();
		$this->assertEquals('', $request->base);
		$this->assertEquals([], $request->query);

		$_GET = [];
		$_GET['/cake_dev/posts/index/add_add'] = '';
		$_SERVER['PHP_SELF'] = '/cake_dev/app/webroot/index.php';
		$_SERVER['REQUEST_URI'] = '/cake_dev/posts/index/add.add';
		$request = new CakeRequest();
		$this->assertEquals('/cake_dev', $request->base);
		$this->assertEquals([], $request->query);
	}

/**
 * Test that a request with urlencoded bits in the main GET parameter are filtered out.
 *
 * @return void
 */
	public function testGetParamWithUrlencodedElement() {
		$_GET = [];
		$_GET['/posts/add/∂∂'] = '';
		$_SERVER['PHP_SELF'] = '/app/webroot/index.php';
		$_SERVER['REQUEST_URI'] = '/posts/add/%E2%88%82%E2%88%82';
		$request = new CakeRequest();
		$this->assertEquals('', $request->base);
		$this->assertEquals([], $request->query);

		$_GET = [];
		$_GET['/cake_dev/posts/add/∂∂'] = '';
		$_SERVER['PHP_SELF'] = '/cake_dev/app/webroot/index.php';
		$_SERVER['REQUEST_URI'] = '/cake_dev/posts/add/%E2%88%82%E2%88%82';
		$request = new CakeRequest();
		$this->assertEquals('/cake_dev', $request->base);
		$this->assertEquals([], $request->query);
	}

/**
 * Generator for environment configurations
 *
 * @return array Environment array
 */
	public static function environmentGenerator() {
		return [
			[
				'IIS - No rewrite base path',
				[
					'App' => [
						'base' => false,
						'baseUrl' => '/index.php',
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'SERVER' => [
						'SCRIPT_NAME' => '/index.php',
						'PATH_TRANSLATED' => 'C:\\Inetpub\\wwwroot',
						'QUERY_STRING' => '',
						'REQUEST_URI' => '/index.php',
						'URL' => '/index.php',
						'SCRIPT_FILENAME' => 'C:\\Inetpub\\wwwroot\\index.php',
						'ORIG_PATH_INFO' => '/index.php',
						'PATH_INFO' => '',
						'ORIG_PATH_TRANSLATED' => 'C:\\Inetpub\\wwwroot\\index.php',
						'DOCUMENT_ROOT' => 'C:\\Inetpub\\wwwroot',
						'PHP_SELF' => '/index.php',
					],
				],
				[
					'base' => '/index.php',
					'webroot' => '/app/webroot/',
					'url' => ''
				],
			],
			[
				'IIS - No rewrite with path, no PHP_SELF',
				[
					'App' => [
						'base' => false,
						'baseUrl' => '/index.php?',
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'SERVER' => [
						'QUERY_STRING' => '/posts/add',
						'REQUEST_URI' => '/index.php?/posts/add',
						'PHP_SELF' => '',
						'URL' => '/index.php?/posts/add',
						'DOCUMENT_ROOT' => 'C:\\Inetpub\\wwwroot',
						'argv' => ['/posts/add'],
						'argc' => 1
					],
				],
				[
					'url' => 'posts/add',
					'base' => '/index.php?',
					'webroot' => '/app/webroot/'
				]
			],
			[
				'IIS - No rewrite sub dir 2',
				[
					'App' => [
						'base' => false,
						'baseUrl' => '/site/index.php',
						'dir' => 'app',
						'webroot' => 'webroot',
					],
					'SERVER' => [
						'SCRIPT_NAME' => '/site/index.php',
						'PATH_TRANSLATED' => 'C:\\Inetpub\\wwwroot',
						'QUERY_STRING' => '',
						'REQUEST_URI' => '/site/index.php',
						'URL' => '/site/index.php',
						'SCRIPT_FILENAME' => 'C:\\Inetpub\\wwwroot\\site\\index.php',
						'DOCUMENT_ROOT' => 'C:\\Inetpub\\wwwroot',
						'PHP_SELF' => '/site/index.php',
						'argv' => [],
						'argc' => 0
					],
				],
				[
					'url' => '',
					'base' => '/site/index.php',
					'webroot' => '/site/app/webroot/'
				],
			],
			[
				'IIS - No rewrite sub dir 2 with path',
				[
					'App' => [
						'base' => false,
						'baseUrl' => '/site/index.php',
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'GET' => ['/posts/add' => ''],
					'SERVER' => [
						'SCRIPT_NAME' => '/site/index.php',
						'PATH_TRANSLATED' => 'C:\\Inetpub\\wwwroot',
						'QUERY_STRING' => '/posts/add',
						'REQUEST_URI' => '/site/index.php/posts/add',
						'URL' => '/site/index.php/posts/add',
						'ORIG_PATH_TRANSLATED' => 'C:\\Inetpub\\wwwroot\\site\\index.php',
						'DOCUMENT_ROOT' => 'C:\\Inetpub\\wwwroot',
						'PHP_SELF' => '/site/index.php/posts/add',
						'argv' => ['/posts/add'],
						'argc' => 1
					],
				],
				[
					'url' => 'posts/add',
					'base' => '/site/index.php',
					'webroot' => '/site/app/webroot/'
				]
			],
			[
				'Apache - No rewrite, document root set to webroot, requesting path',
				[
					'App' => [
						'base' => false,
						'baseUrl' => '/index.php',
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'SERVER' => [
						'DOCUMENT_ROOT' => '/Library/WebServer/Documents/site/app/webroot',
						'SCRIPT_FILENAME' => '/Library/WebServer/Documents/site/app/webroot/index.php',
						'QUERY_STRING' => '',
						'REQUEST_URI' => '/index.php/posts/index',
						'SCRIPT_NAME' => '/index.php',
						'PATH_INFO' => '/posts/index',
						'PHP_SELF' => '/index.php/posts/index',
					],
				],
				[
					'url' => 'posts/index',
					'base' => '/index.php',
					'webroot' => '/'
				],
			],
			[
				'Apache - No rewrite, document root set to webroot, requesting root',
				[
					'App' => [
						'base' => false,
						'baseUrl' => '/index.php',
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'SERVER' => [
						'DOCUMENT_ROOT' => '/Library/WebServer/Documents/site/app/webroot',
						'SCRIPT_FILENAME' => '/Library/WebServer/Documents/site/app/webroot/index.php',
						'QUERY_STRING' => '',
						'REQUEST_URI' => '/index.php',
						'SCRIPT_NAME' => '/index.php',
						'PATH_INFO' => '',
						'PHP_SELF' => '/index.php',
					],
				],
				[
					'url' => '',
					'base' => '/index.php',
					'webroot' => '/'
				],
			],
			[
				'Apache - No rewrite, document root set above top level cake dir, requesting path',
				[
					'App' => [
						'base' => false,
						'baseUrl' => '/site/index.php',
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'SERVER' => [
						'SERVER_NAME' => 'localhost',
						'DOCUMENT_ROOT' => '/Library/WebServer/Documents',
						'SCRIPT_FILENAME' => '/Library/WebServer/Documents/site/index.php',
						'REQUEST_URI' => '/site/index.php/posts/index',
						'SCRIPT_NAME' => '/site/index.php',
						'PATH_INFO' => '/posts/index',
						'PHP_SELF' => '/site/index.php/posts/index',
					],
				],
				[
					'url' => 'posts/index',
					'base' => '/site/index.php',
					'webroot' => '/site/app/webroot/',
				],
			],
			[
				'Apache - No rewrite, document root set above top level cake dir, request root, no PATH_INFO',
				[
					'App' => [
						'base' => false,
						'baseUrl' => '/site/index.php',
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'SERVER' => [
						'SERVER_NAME' => 'localhost',
						'DOCUMENT_ROOT' => '/Library/WebServer/Documents',
						'SCRIPT_FILENAME' => '/Library/WebServer/Documents/site/index.php',
						'REQUEST_URI' => '/site/index.php/',
						'SCRIPT_NAME' => '/site/index.php',
						'PHP_SELF' => '/site/index.php/',
					],
				],
				[
					'url' => '',
					'base' => '/site/index.php',
					'webroot' => '/site/app/webroot/',
				],
			],
			[
				'Apache - No rewrite, document root set above top level cake dir, request path, with GET',
				[
					'App' => [
						'base' => false,
						'baseUrl' => '/site/index.php',
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'GET' => ['a' => 'b', 'c' => 'd'],
					'SERVER' => [
						'SERVER_NAME' => 'localhost',
						'DOCUMENT_ROOT' => '/Library/WebServer/Documents',
						'SCRIPT_FILENAME' => '/Library/WebServer/Documents/site/index.php',
						'REQUEST_URI' => '/site/index.php/posts/index?a=b&c=d',
						'SCRIPT_NAME' => '/site/index.php',
						'PATH_INFO' => '/posts/index',
						'PHP_SELF' => '/site/index.php/posts/index',
						'QUERY_STRING' => 'a=b&c=d'
					],
				],
				[
					'urlParams' => ['a' => 'b', 'c' => 'd'],
					'url' => 'posts/index',
					'base' => '/site/index.php',
					'webroot' => '/site/app/webroot/',
				],
			],
			[
				'Apache - w/rewrite, document root set above top level cake dir, request root, no PATH_INFO',
				[
					'App' => [
						'base' => false,
						'baseUrl' => false,
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'SERVER' => [
						'SERVER_NAME' => 'localhost',
						'DOCUMENT_ROOT' => '/Library/WebServer/Documents',
						'SCRIPT_FILENAME' => '/Library/WebServer/Documents/site/index.php',
						'REQUEST_URI' => '/site/',
						'SCRIPT_NAME' => '/site/app/webroot/index.php',
						'PHP_SELF' => '/site/app/webroot/index.php',
					],
				],
				[
					'url' => '',
					'base' => '/site',
					'webroot' => '/site/',
				],
			],
			[
				'Apache - w/rewrite, document root above top level cake dir, request root, no PATH_INFO/REQUEST_URI',
				[
					'App' => [
						'base' => false,
						'baseUrl' => false,
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'SERVER' => [
						'SERVER_NAME' => 'localhost',
						'DOCUMENT_ROOT' => '/Library/WebServer/Documents',
						'SCRIPT_FILENAME' => '/Library/WebServer/Documents/site/index.php',
						'SCRIPT_NAME' => '/site/app/webroot/index.php',
						'PHP_SELF' => '/site/app/webroot/index.php',
						'PATH_INFO' => null,
						'REQUEST_URI' => null,
					],
				],
				[
					'url' => '',
					'base' => '/site',
					'webroot' => '/site/',
				],
			],
			[
				'Apache - w/rewrite, document root set to webroot, request root, no PATH_INFO/REQUEST_URI',
				[
					'App' => [
						'base' => false,
						'baseUrl' => false,
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'SERVER' => [
						'SERVER_NAME' => 'localhost',
						'DOCUMENT_ROOT' => '/Library/WebServer/Documents/site/app/webroot',
						'SCRIPT_FILENAME' => '/Library/WebServer/Documents/site/app/webroot/index.php',
						'SCRIPT_NAME' => '/index.php',
						'PHP_SELF' => '/index.php',
						'PATH_INFO' => null,
						'REQUEST_URI' => null,
					],
				],
				[
					'url' => '',
					'base' => '',
					'webroot' => '/',
				],
			],
			[
				'Apache - w/rewrite, document root set above top level cake dir, request root, absolute REQUEST_URI',
				[
					'App' => [
						'base' => false,
						'baseUrl' => false,
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'SERVER' => [
						'SERVER_NAME' => 'localhost',
						'DOCUMENT_ROOT' => '/Library/WebServer/Documents',
						'SCRIPT_FILENAME' => '/Library/WebServer/Documents/site/index.php',
						'REQUEST_URI' => '/site/posts/index',
						'SCRIPT_NAME' => '/site/app/webroot/index.php',
						'PHP_SELF' => '/site/app/webroot/index.php',
					],
				],
				[
					'url' => 'posts/index',
					'base' => '/site',
					'webroot' => '/site/',
				],
			],
			[
				'Nginx - w/rewrite, document root set to webroot, request root, no PATH_INFO',
				[
					'App' => [
						'base' => false,
						'baseUrl' => false,
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'GET' => ['/posts/add' => ''],
					'SERVER' => [
						'SERVER_NAME' => 'localhost',
						'DOCUMENT_ROOT' => '/Library/WebServer/Documents/site/app/webroot',
						'SCRIPT_FILENAME' => '/Library/WebServer/Documents/site/app/webroot/index.php',
						'SCRIPT_NAME' => '/index.php',
						'QUERY_STRING' => '/posts/add&',
						'PHP_SELF' => '/index.php',
						'PATH_INFO' => null,
						'REQUEST_URI' => '/posts/add',
					],
				],
				[
					'url' => 'posts/add',
					'base' => '',
					'webroot' => '/',
					'urlParams' => []
				],
			],
			[
				'Nginx - w/rewrite, document root set above top level cake dir, request root, no PATH_INFO, base parameter set',
				[
					'App' => [
						'base' => false,
						'baseUrl' => false,
						'dir' => 'app',
						'webroot' => 'webroot'
					],
					'GET' => ['/site/posts/add' => ''],
					'SERVER' => [
						'SERVER_NAME' => 'localhost',
						'DOCUMENT_ROOT' => '/Library/WebServer/Documents',
						'SCRIPT_FILENAME' => '/Library/WebServer/Documents/site/app/webroot/index.php',
						'SCRIPT_NAME' => '/site/app/webroot/index.php',
						'QUERY_STRING' => '/site/posts/add&',
						'PHP_SELF' => '/site/app/webroot/index.php',
						'PATH_INFO' => null,
						'REQUEST_URI' => '/site/posts/add',
					],
				],
				[
					'url' => 'posts/add',
					'base' => '/site',
					'webroot' => '/site/',
					'urlParams' => []
				],
			],
		];
	}

/**
 * Test environment detection
 *
 * @dataProvider environmentGenerator
 * @param $name
 * @param $env
 * @param $expected
 * @return void
 */
	public function testEnvironmentDetection($name, $env, $expected) {
		$_GET = [];
		$this->_loadEnvironment($env);

		$request = new CakeRequest();
		$this->assertEquals($expected['url'], $request->url, "url error");
		$this->assertEquals($expected['base'], $request->base, "base error");
		$this->assertEquals($expected['webroot'], $request->webroot, "webroot error");
		if (isset($expected['urlParams'])) {
			$this->assertEquals($expected['urlParams'], $request->query, "GET param mismatch");
		}
	}

/**
 * Test the query() method
 *
 * @return void
 */
	public function testQuery() {
		$_GET = [];
		$_GET['foo'] = 'bar';

		$request = new CakeRequest();

		$result = $request->query('foo');
		$this->assertEquals('bar', $result);

		$result = $request->query('imaginary');
		$this->assertNull($result);
	}

/**
 * Test the query() method with arrays passed via $_GET
 *
 * @return void
 */
	public function testQueryWithArray() {
		$_GET = [];
		$_GET['test'] = ['foo', 'bar'];

		$request = new CakeRequest();

		$result = $request->query('test');
		$this->assertEquals(['foo', 'bar'], $result);

		$result = $request->query('test.1');
		$this->assertEquals('bar', $result);

		$result = $request->query('test.2');
		$this->assertNull($result);
	}

/**
 * Test the data() method reading
 *
 * @return void
 */
	public function testDataReading() {
		$_POST['data'] = [
			'Model' => [
				'field' => 'value'
			]
		];
		$request = new CakeRequest('posts/index');
		$result = $request->data('Model');
		$this->assertEquals($_POST['data']['Model'], $result);

		$result = $request->data('Model.imaginary');
		$this->assertNull($result);
	}

/**
 * Test writing with data()
 *
 * @return void
 */
	public function testDataWriting() {
		$_POST['data'] = [
			'Model' => [
				'field' => 'value'
			]
		];
		$request = new CakeRequest('posts/index');
		$result = $request->data('Model.new_value', 'new value');
		$this->assertSame($result, $request, 'Return was not $this');

		$this->assertEquals('new value', $request->data['Model']['new_value']);

		$request->data('Post.title', 'New post')->data('Comment.1.author', 'Mark');
		$this->assertEquals('New post', $request->data['Post']['title']);
		$this->assertEquals('Mark', $request->data['Comment']['1']['author']);
	}

/**
 * Test writing falsey values.
 *
 * @return void
 */
	public function testDataWritingFalsey() {
		$request = new CakeRequest('posts/index');

		$request->data('Post.null', null);
		$this->assertNull($request->data['Post']['null']);

		$request->data('Post.false', false);
		$this->assertFalse($request->data['Post']['false']);

		$request->data('Post.zero', 0);
		$this->assertSame(0, $request->data['Post']['zero']);

		$request->data('Post.empty', '');
		$this->assertSame('', $request->data['Post']['empty']);
	}

/**
 * Test reading params
 *
 * @dataProvider paramReadingDataProvider
 */
	public function testParamReading($toRead, $expected) {
		$request = new CakeRequest('/');
		$request->addParams([
			'action' => 'index',
			'foo' => 'bar',
			'baz' => [
				'a' => [
					'b' => 'c',
				],
			],
			'admin' => true,
			'truthy' => 1,
			'zero' => '0',
		]);
		$this->assertEquals($expected, $request->param($toRead));
	}

/**
 * Data provider for testing reading values with CakeRequest::param()
 *
 * @return array
 */
	public function paramReadingDataProvider() {
		return [
			[
				'action',
				'index',
			],
			[
				'baz',
				[
					'a' => [
						'b' => 'c',
					],
				],
			],
			[
				'baz.a.b',
				'c',
			],
			[
				'does_not_exist',
				false,
			],
			[
				'admin',
				true,
			],
			[
				'truthy',
				1,
			],
			[
				'zero',
				'0',
			],
		];
	}

/**
 * test writing request params with param()
 *
 * @return void
 */
	public function testParamWriting() {
		$request = new CakeRequest('/');
		$request->addParams([
			'action' => 'index',
		]);

		$this->assertInstanceOf('CakeRequest', $request->param('some', 'thing'), 'Method has not returned $this');

		$request->param('Post.null', null);
		$this->assertNull($request->params['Post']['null']);

		$request->param('Post.false', false);
		$this->assertFalse($request->params['Post']['false']);

		$request->param('Post.zero', 0);
		$this->assertSame(0, $request->params['Post']['zero']);

		$request->param('Post.empty', '');
		$this->assertSame('', $request->params['Post']['empty']);

		$this->assertSame('index', $request->action);
		$request->param('action', 'edit');
		$this->assertSame('edit', $request->action);
	}

/**
 * Test accept language
 *
 * @return void
 */
	public function testAcceptLanguage() {
		// Weird language
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'inexistent,en-ca';
		$result = CakeRequest::acceptLanguage();
		$this->assertEquals(['inexistent', 'en-ca'], $result, 'Languages do not match');

		// No qualifier
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'es_mx,en_ca';
		$result = CakeRequest::acceptLanguage();
		$this->assertEquals(['es-mx', 'en-ca'], $result, 'Languages do not match');

		// With qualifier
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,en;q=0.8,pt-BR;q=0.6,pt;q=0.4';
		$result = CakeRequest::acceptLanguage();
		$this->assertEquals(['en-us', 'en', 'pt-br', 'pt'], $result, 'Languages do not match');

		// With spaces
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'da, en-gb;q=0.8, en;q=0.7';
		$result = CakeRequest::acceptLanguage();
		$this->assertEquals(['da', 'en-gb', 'en'], $result, 'Languages do not match');

		// Checking if requested
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'es_mx,en_ca';
		CakeRequest::acceptLanguage();

		$result = CakeRequest::acceptLanguage('en-ca');
		$this->assertTrue($result);

		$result = CakeRequest::acceptLanguage('en-CA');
		$this->assertTrue($result);

		$result = CakeRequest::acceptLanguage('en-us');
		$this->assertFalse($result);

		$result = CakeRequest::acceptLanguage('en-US');
		$this->assertFalse($result);
	}

/**
 * Test the here() method
 *
 * @return void
 */
	public function testHere() {
		Configure::write('App.base', '/base_path');
		$_GET = ['test' => 'value'];
		$request = new CakeRequest('/posts/add/1/name:value');

		$result = $request->here();
		$this->assertEquals('/base_path/posts/add/1/name:value?test=value', $result);

		$result = $request->here(false);
		$this->assertEquals('/posts/add/1/name:value?test=value', $result);

		$request = new CakeRequest('/posts/base_path/1/name:value');
		$result = $request->here();
		$this->assertEquals('/base_path/posts/base_path/1/name:value?test=value', $result);

		$result = $request->here(false);
		$this->assertEquals('/posts/base_path/1/name:value?test=value', $result);
	}

/**
 * Test the here() with space in URL
 *
 * @return void
 */
	public function testHereWithSpaceInUrl() {
		Configure::write('App.base', '');
		$_GET = ['/admin/settings/settings/prefix/Access_Control' => ''];
		$request = new CakeRequest('/admin/settings/settings/prefix/Access%20Control');

		$result = $request->here();
		$this->assertEquals('/admin/settings/settings/prefix/Access%20Control', $result);
	}

/**
 * Test the input() method.
 *
 * @return void
 */
	public function testSetInput() {
		$request = new CakeRequest('/');

		$request->setInput('I came from setInput');
		$result = $request->input();
		$this->assertEquals('I came from setInput', $result);

		$result = $request->input();
		$this->assertEquals('I came from setInput', $result);
	}

/**
 * Test the input() method.
 *
 * @return void
 */
	public function testInput() {
		$request = $this->getMock('CakeRequest', ['_readInput']);
		$request->expects($this->once())->method('_readInput')
			->will($this->returnValue('I came from stdin'));

		$result = $request->input();
		$this->assertEquals('I came from stdin', $result);
	}

/**
 * Test input() decoding.
 *
 * @return void
 */
	public function testInputDecode() {
		$request = $this->getMock('CakeRequest', ['_readInput']);
		$request->expects($this->once())->method('_readInput')
			->will($this->returnValue('{"name":"value"}'));

		$result = $request->input('json_decode');
		$this->assertEquals(['name' => 'value'], (array)$result);
	}

/**
 * Test input() decoding with additional arguments.
 *
 * @return void
 */
	public function testInputDecodeExtraParams() {
		$xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<post>
	<title id="title">Test</title>
</post>
XML;

		$request = $this->getMock('CakeRequest', ['_readInput']);
		$request->expects($this->once())->method('_readInput')
			->will($this->returnValue($xml));

		$result = $request->input('Xml::build', ['return' => 'domdocument']);
		$this->assertInstanceOf('DOMDocument', $result);
		$this->assertEquals(
			'Test',
			$result->getElementsByTagName('title')->item(0)->childNodes->item(0)->wholeText
		);
	}

/**
 * Test is('requested') and isRequested()
 *
 * @return void
 */
	public function testIsRequested() {
		$request = new CakeRequest('/posts/index');
		$request->addParams([
			'controller' => 'posts',
			'action' => 'index',
			'plugin' => null,
			'requested' => 1
		]);
		$this->assertTrue($request->is('requested'));
		$this->assertTrue($request->isRequested());

		$request = new CakeRequest('/posts/index');
		$request->addParams([
			'controller' => 'posts',
			'action' => 'index',
			'plugin' => null,
		]);
		$this->assertFalse($request->is('requested'));
		$this->assertFalse($request->isRequested());
	}

/**
 * Test allowMethod method
 *
 * @return void
 */
	public function testAllowMethod() {
		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$request = new CakeRequest('/posts/edit/1');

		$this->assertTrue($request->allowMethod(['put']));

		// BC check
		$this->assertTrue($request->onlyAllow(['put']));

		$_SERVER['REQUEST_METHOD'] = 'DELETE';
		$this->assertTrue($request->allowMethod('post', 'delete'));

		// BC check
		$this->assertTrue($request->onlyAllow('post', 'delete'));
	}

/**
 * Test allowMethod throwing exception
 *
 * @return void
 */
	public function testAllowMethodException() {
		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$request = new CakeRequest('/posts/edit/1');

		try {
			$request->allowMethod('POST', 'DELETE');
			$this->fail('An expected exception has not been raised.');
		} catch (MethodNotAllowedException $e) {
			$this->assertEquals(['Allow' => 'POST, DELETE'], $e->responseHeader());
		}

		$this->setExpectedException('MethodNotAllowedException');
		$request->allowMethod('POST');
	}

/**
 * Tests that overriding the method to GET will clean all request
 * data, to better simulate a GET request.
 *
 * @return void
 */
	public function testMethodOverrideEmptyData() {
		$_POST = ['_method' => 'GET', 'foo' => 'bar'];
		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$request = new CakeRequest('/posts/edit/1');
		$this->assertEmpty($request->data);

		$_POST = ['foo' => 'bar'];
		$_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'GET';
		$request = new CakeRequest('/posts/edit/1');
		$this->assertEmpty($request->data);
	}

/**
 * loadEnvironment method
 *
 * @param array $env
 * @return void
 */
	protected function _loadEnvironment($env) {
		if (isset($env['App'])) {
			Configure::write('App', $env['App']);
		}

		if (isset($env['GET'])) {
			foreach ($env['GET'] as $key => $val) {
				$_GET[$key] = $val;
			}
		}

		if (isset($env['POST'])) {
			foreach ($env['POST'] as $key => $val) {
				$_POST[$key] = $val;
			}
		}

		if (isset($env['SERVER'])) {
			foreach ($env['SERVER'] as $key => $val) {
				$_SERVER[$key] = $val;
			}
		}
	}

}
