<?php
/**
 * HtmlHelperTest file
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

App::uses('Controller', 'Controller');
App::uses('Helper', 'View');
App::uses('AppHelper', 'View/Helper');
App::uses('HtmlHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');
App::uses('ClassRegistry', 'Utility');
App::uses('Folder', 'Utility');
App::uses('CakePlugin', 'Core');

if (!defined('FULL_BASE_URL')) {
	define('FULL_BASE_URL', 'http://cakephp.org');
}

/**
 * TheHtmlTestController class
 *
 * @package       Cake.Test.Case.View.Helper
 */
class TheHtmlTestController extends Controller {

/**
 * name property
 *
 * @var string
 */
	public $name = 'TheTest';

/**
 * uses property
 *
 * @var mixed
 */
	public $uses = null;
}

class TestHtmlHelper extends HtmlHelper {

/**
 * expose a method as public
 *
 * @param string $options
 * @param string $exclude
 * @param string $insertBefore
 * @param string $insertAfter
 * @return void
 */
	public function parseAttributes($options, $exclude = null, $insertBefore = ' ', $insertAfter = null) {
		return $this->_parseAttributes($options, $exclude, $insertBefore, $insertAfter);
	}

/**
 * Get a protected attribute value
 *
 * @param string $attribute
 * @return mixed
 */
	public function getAttribute($attribute) {
		if (!isset($this->{$attribute})) {
			return null;
		}
		return $this->{$attribute};
	}

}

/**
 * Html5TestHelper class
 *
 * @package       Cake.Test.Case.View.Helper
 */
class Html5TestHelper extends TestHtmlHelper {

/**
 * Minimized
 *
 * @var array
 */
	protected $_minimizedAttributes = ['require', 'checked'];

/**
 * Allow compact use in HTML
 *
 * @var string
 */
	protected $_minimizedAttributeFormat = '%s';

/**
 * Test to attribute format
 *
 * @var string
 */
	protected $_attributeFormat = 'data-%s="%s"';
}

/**
 * HtmlHelperTest class
 *
 * @package       Cake.Test.Case.View.Helper
 */
class HtmlHelperTest extends CakeTestCase {

/**
 * Regexp for CDATA start block
 *
 * @var string
 */
	public $cDataStart = 'preg:/^\/\/<!\[CDATA\[[\n\r]*/';

/**
 * Regexp for CDATA end block
 *
 * @var string
 */
	public $cDataEnd = 'preg:/[^\]]*\]\]\>[\s\r\n]*/';

/**
 * html property
 *
 * @var object
 */
	public $Html = null;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->View = $this->getMock('View', ['append'], [new TheHtmlTestController()]);
		$this->Html = new TestHtmlHelper($this->View);
		$this->Html->request = new CakeRequest(null, false);
		$this->Html->request->webroot = '';

		App::build([
			'Plugin' => [CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS]
		]);

		Configure::write('Asset.timestamp', false);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Html, $this->View);
	}

/**
 * testDocType method
 *
 * @return void
 */
	public function testDocType() {
		$result = $this->Html->docType();
		$expected = '<!DOCTYPE html>';
		$this->assertEquals($expected, $result);

		$result = $this->Html->docType('html4-strict');
		$expected = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
		$this->assertEquals($expected, $result);

		$this->assertNull($this->Html->docType('non-existing-doctype'));
	}

/**
 * testLink method
 *
 * @return void
 */
	public function testLink() {
		Router::connect('/:controller/:action/*');

		$this->Html->request->webroot = '';

		$result = $this->Html->link('/home');
		$expected = ['a' => ['href' => '/home'], 'preg:/\/home/', '/a'];
		$this->assertTags($result, $expected);

		$result = $this->Html->link(['action' => 'login', '<[You]>']);
		$expected = [
			'a' => ['href' => '/login/%3C%5BYou%5D%3E'],
			'preg:/\/login\/&lt;\[You\]&gt;/',
			'/a'
		];
		$this->assertTags($result, $expected);

		Router::reload();

		$result = $this->Html->link('Posts', ['controller' => 'posts', 'action' => 'index', 'full_base' => true]);
		$expected = ['a' => ['href' => Router::fullBaseUrl() . '/posts'], 'Posts', '/a'];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('Home', '/home', ['confirm' => 'Are you sure you want to do this?']);
		$expected = [
			'a' => ['href' => '/home', 'onclick' => 'if (confirm(&quot;Are you sure you want to do this?&quot;)) { return true; } return false;'],
			'Home',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('Home', '/home', ['escape' => false, 'confirm' => 'Confirm\'s "nightmares"']);
		$expected = [
			'a' => ['href' => '/home', 'onclick' => 'if (confirm(&quot;Confirm&#039;s \&quot;nightmares\&quot;&quot;)) { return true; } return false;'],
			'Home',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('Home', '/home', ['default' => false]);
		$expected = [
			'a' => ['href' => '/home', 'onclick' => 'event.returnValue = false; return false;'],
			'Home',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('Home', '/home', ['default' => false, 'onclick' => 'someFunction();']);
		$expected = [
			'a' => ['href' => '/home', 'onclick' => 'someFunction(); event.returnValue = false; return false;'],
			'Home',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('Next >', '#');
		$expected = [
			'a' => ['href' => '#'],
			'Next &gt;',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('Next >', '#', ['escape' => true]);
		$expected = [
			'a' => ['href' => '#'],
			'Next &gt;',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('Next >', '#', ['escape' => 'utf-8']);
		$expected = [
			'a' => ['href' => '#'],
			'Next &gt;',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('Next >', '#', ['escape' => false]);
		$expected = [
			'a' => ['href' => '#'],
			'Next >',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('Next >', '#', [
			'title' => 'to escape &#8230; or not escape?',
			'escape' => false
		]);
		$expected = [
			'a' => ['href' => '#', 'title' => 'to escape &#8230; or not escape?'],
			'Next >',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('Next >', '#', [
			'title' => 'to escape &#8230; or not escape?',
			'escape' => true
		]);
		$expected = [
			'a' => ['href' => '#', 'title' => 'to escape &amp;#8230; or not escape?'],
			'Next &gt;',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('Next >', '#', [
			'title' => 'Next >',
			'escapeTitle' => false
		]);
		$expected = [
			'a' => ['href' => '#', 'title' => 'Next &gt;'],
			'Next >',
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('Original size', [
			'controller' => 'images', 'action' => 'view', 3, '?' => ['height' => 100, 'width' => 200]
		]);
		$expected = [
			'a' => ['href' => '/images/view/3?height=100&amp;width=200'],
			'Original size',
			'/a'
		];
		$this->assertTags($result, $expected);

		Configure::write('Asset.timestamp', false);

		$result = $this->Html->link($this->Html->image('test.gif'), '#', ['escape' => false]);
		$expected = [
			'a' => ['href' => '#'],
			'img' => ['src' => 'img/test.gif', 'alt' => ''],
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link($this->Html->image('test.gif'), '#', [
			'title' => 'hey "howdy"',
			'escapeTitle' => false
		]);
		$expected = [
			'a' => ['href' => '#', 'title' => 'hey &quot;howdy&quot;'],
			'img' => ['src' => 'img/test.gif', 'alt' => ''],
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->image('test.gif', ['url' => '#']);
		$expected = [
			'a' => ['href' => '#'],
			'img' => ['src' => 'img/test.gif', 'alt' => ''],
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link($this->Html->image('../favicon.ico'), '#', ['escape' => false]);
		$expected = [
			'a' => ['href' => '#'],
			'img' => ['src' => 'img/../favicon.ico', 'alt' => ''],
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->image('../favicon.ico', ['url' => '#']);
		$expected = [
			'a' => ['href' => '#'],
			'img' => ['src' => 'img/../favicon.ico', 'alt' => ''],
			'/a'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('http://www.example.org?param1=value1&param2=value2');
		$expected = ['a' => ['href' => 'http://www.example.org?param1=value1&amp;param2=value2'], 'http://www.example.org?param1=value1&amp;param2=value2', '/a'];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('alert', 'javascript:alert(\'cakephp\');');
		$expected = ['a' => ['href' => 'javascript:alert(&#039;cakephp&#039;);'], 'alert', '/a'];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('write me', 'mailto:example@cakephp.org');
		$expected = ['a' => ['href' => 'mailto:example@cakephp.org'], 'write me', '/a'];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('call me on 0123465-798', 'tel:0123465-798');
		$expected = ['a' => ['href' => 'tel:0123465-798'], 'call me on 0123465-798', '/a'];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('text me on 0123465-798', 'sms:0123465-798');
		$expected = ['a' => ['href' => 'sms:0123465-798'], 'text me on 0123465-798', '/a'];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('say hello to 0123465-798', 'sms:0123465-798?body=hello there');
		$expected = ['a' => ['href' => 'sms:0123465-798?body=hello there'], 'say hello to 0123465-798', '/a'];
		$this->assertTags($result, $expected);

		$result = $this->Html->link('say hello to 0123465-798', 'sms:0123465-798?body=hello "cakephp"');
		$expected = ['a' => ['href' => 'sms:0123465-798?body=hello &quot;cakephp&quot;'], 'say hello to 0123465-798', '/a'];
		$this->assertTags($result, $expected);
	}

/**
 * testImageTag method
 *
 * @return void
 */
	public function testImageTag() {
		$this->Html->request->webroot = '';

		$result = $this->Html->image('test.gif');
		$this->assertTags($result, ['img' => ['src' => 'img/test.gif', 'alt' => '']]);

		$result = $this->Html->image('http://google.com/logo.gif');
		$this->assertTags($result, ['img' => ['src' => 'http://google.com/logo.gif', 'alt' => '']]);

		$result = $this->Html->image('//google.com/logo.gif');
		$this->assertTags($result, ['img' => ['src' => '//google.com/logo.gif', 'alt' => '']]);

		$result = $this->Html->image(['controller' => 'test', 'action' => 'view', 1, 'ext' => 'gif']);
		$this->assertTags($result, ['img' => ['src' => '/test/view/1.gif', 'alt' => '']]);

		$result = $this->Html->image('/test/view/1.gif');
		$this->assertTags($result, ['img' => ['src' => '/test/view/1.gif', 'alt' => '']]);
	}

/**
 * Test image() with query strings.
 *
 * @return void
 */
	public function testImageQueryString() {
		$result = $this->Html->image('test.gif?one=two&three=four');
		$this->assertTags($result, ['img' => ['src' => 'img/test.gif?one=two&amp;three=four', 'alt' => '']]);

		$result = $this->Html->image([
			'controller' => 'images',
			'action' => 'display',
			'test',
			'?' => ['one' => 'two', 'three' => 'four']
		]);
		$this->assertTags($result, ['img' => ['src' => '/images/display/test?one=two&amp;three=four', 'alt' => '']]);
	}

/**
 * Test that image works with pathPrefix.
 *
 * @return void
 */
	public function testImagePathPrefix() {
		$result = $this->Html->image('test.gif', ['pathPrefix' => '/my/custom/path/']);
		$this->assertTags($result, ['img' => ['src' => '/my/custom/path/test.gif', 'alt' => '']]);

		$result = $this->Html->image('test.gif', ['pathPrefix' => 'http://cakephp.org/assets/img/']);
		$this->assertTags($result, ['img' => ['src' => 'http://cakephp.org/assets/img/test.gif', 'alt' => '']]);

		$result = $this->Html->image('test.gif', ['pathPrefix' => '//cakephp.org/assets/img/']);
		$this->assertTags($result, ['img' => ['src' => '//cakephp.org/assets/img/test.gif', 'alt' => '']]);

		$previousConfig = Configure::read('App.imageBaseUrl');
		Configure::write('App.imageBaseUrl', '//cdn.cakephp.org/img/');
		$result = $this->Html->image('test.gif');
		$this->assertTags($result, ['img' => ['src' => '//cdn.cakephp.org/img/test.gif', 'alt' => '']]);
		Configure::write('App.imageBaseUrl', $previousConfig);
	}

/**
 * Test that image() works with fullBase and a webroot not equal to /
 *
 * @return void
 */
	public function testImageWithFullBase() {
		$result = $this->Html->image('test.gif', ['fullBase' => true]);
		$here = $this->Html->url('/', true);
		$this->assertTags($result, ['img' => ['src' => $here . 'img/test.gif', 'alt' => '']]);

		$result = $this->Html->image('sub/test.gif', ['fullBase' => true]);
		$here = $this->Html->url('/', true);
		$this->assertTags($result, ['img' => ['src' => $here . 'img/sub/test.gif', 'alt' => '']]);

		$request = $this->Html->request;
		$request->webroot = '/myproject/';
		$request->base = '/myproject';
		Router::setRequestInfo($request);

		$result = $this->Html->image('sub/test.gif', ['fullBase' => true]);
		$here = $this->Html->url('/', true);
		$this->assertTags($result, ['img' => ['src' => $here . 'img/sub/test.gif', 'alt' => '']]);
	}

/**
 * test image() with Asset.timestamp
 *
 * @return void
 */
	public function testImageWithTimestampping() {
		Configure::write('Asset.timestamp', 'force');

		$this->Html->request->webroot = '/';
		$result = $this->Html->image('cake.icon.png');
		$this->assertTags($result, ['img' => ['src' => 'preg:/\/img\/cake\.icon\.png\?\d+/', 'alt' => '']]);

		Configure::write('debug', 0);
		Configure::write('Asset.timestamp', 'force');

		$result = $this->Html->image('cake.icon.png');
		$this->assertTags($result, ['img' => ['src' => 'preg:/\/img\/cake\.icon\.png\?\d+/', 'alt' => '']]);

		$this->Html->request->webroot = '/testing/longer/';
		$result = $this->Html->image('cake.icon.png');
		$expected = [
			'img' => ['src' => 'preg:/\/testing\/longer\/img\/cake\.icon\.png\?[0-9]+/', 'alt' => '']
		];
		$this->assertTags($result, $expected);
	}

/**
 * Tests creation of an image tag using a theme and asset timestamping
 *
 * @return void
 */
	public function testImageTagWithTheme() {
		$this->skipIf(!is_writable(WWW_ROOT), 'Cannot write to webroot.');
		$themeExists = is_dir(WWW_ROOT . 'theme');

		App::uses('File', 'Utility');

		$testfile = WWW_ROOT . 'theme' . DS . 'test_theme' . DS . 'img' . DS . '__cake_test_image.gif';
		new File($testfile, true);

		App::build([
			'View' => [CAKE . 'Test' . DS . 'test_app' . DS . 'View' . DS]
		]);
		Configure::write('Asset.timestamp', true);
		Configure::write('debug', 1);

		$this->Html->request->webroot = '/';
		$this->Html->theme = 'test_theme';
		$result = $this->Html->image('__cake_test_image.gif');
		$this->assertTags($result, [
			'img' => [
				'src' => 'preg:/\/theme\/test_theme\/img\/__cake_test_image\.gif\?\d+/',
				'alt' => ''
		]]);

		$this->Html->request->webroot = '/testing/';
		$result = $this->Html->image('__cake_test_image.gif');

		$this->assertTags($result, [
			'img' => [
				'src' => 'preg:/\/testing\/theme\/test_theme\/img\/__cake_test_image\.gif\?\d+/',
				'alt' => ''
		]]);

		$dir = new Folder(WWW_ROOT . 'theme' . DS . 'test_theme');
		$dir->delete();
		if (!$themeExists) {
			$dir = new Folder(WWW_ROOT . 'theme');
			$dir->delete();
		}
	}

/**
 * test theme assets in main webroot path
 *
 * @return void
 */
	public function testThemeAssetsInMainWebrootPath() {
		App::build([
			'View' => [CAKE . 'Test' . DS . 'test_app' . DS . 'View' . DS]
		]);
		$webRoot = Configure::read('App.www_root');
		Configure::write('App.www_root', CAKE . 'Test' . DS . 'test_app' . DS . 'webroot' . DS);

		$this->Html->theme = 'test_theme';
		$result = $this->Html->css('webroot_test');
		$expected = [
			'link' => ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => 'preg:/.*theme\/test_theme\/css\/webroot_test\.css/']
		];
		$this->assertTags($result, $expected);

		$this->Html->theme = 'test_theme';
		$result = $this->Html->css('theme_webroot');
		$expected = [
			'link' => ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => 'preg:/.*theme\/test_theme\/css\/theme_webroot\.css/']
		];
		$this->assertTags($result, $expected);

		Configure::write('App.www_root', $webRoot);
	}

/**
 * testStyle method
 *
 * @return void
 */
	public function testStyle() {
		$result = $this->Html->style('display: none;');
		$this->assertEquals('display: none;', $result);

		$result = $this->Html->style(['display' => 'none', 'margin' => '10px']);
		$expected = 'display:none; margin:10px;';
		$this->assertRegExp('/^display\s*:\s*none\s*;\s*margin\s*:\s*10px\s*;?$/', $expected);

		$result = $this->Html->style(['display' => 'none', 'margin' => '10px'], false);
		$lines = explode("\n", $result);
		$this->assertRegExp('/^\s*display\s*:\s*none\s*;\s*$/', $lines[0]);
		$this->assertRegExp('/^\s*margin\s*:\s*10px\s*;?$/', $lines[1]);
	}

/**
 * testCssLink method
 *
 * @return void
 */
	public function testCssLink() {
		Configure::write('Asset.filter.css', false);

		$result = $this->Html->css('screen');
		$expected = [
			'link' => ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => 'preg:/.*css\/screen\.css/']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->css('screen.css');
		$this->assertTags($result, $expected);

		CakePlugin::load('TestPlugin');
		$result = $this->Html->css('TestPlugin.style', ['plugin' => false]);
		$expected['link']['href'] = 'preg:/.*css\/TestPlugin\.style\.css/';
		$this->assertTags($result, $expected);
		CakePlugin::unload('TestPlugin');

		$result = $this->Html->css('my.css.library');
		$expected['link']['href'] = 'preg:/.*css\/my\.css\.library\.css/';
		$this->assertTags($result, $expected);

		$result = $this->Html->css('screen.css?1234');
		$expected['link']['href'] = 'preg:/.*css\/screen\.css\?1234/';
		$this->assertTags($result, $expected);

		$result = $this->Html->css('screen.css?with=param&other=param');
		$expected['link']['href'] = 'css/screen.css?with=param&amp;other=param';
		$this->assertTags($result, $expected);

		$result = $this->Html->css('http://whatever.com/screen.css?1234');
		$expected['link']['href'] = 'preg:/http:\/\/.*\/screen\.css\?1234/';
		$this->assertTags($result, $expected);

		$result = $this->Html->css('cake.generic', ['pathPrefix' => '/my/custom/path/']);
		$expected['link']['href'] = '/my/custom/path/cake.generic.css';
		$this->assertTags($result, $expected);

		$result = $this->Html->css('cake.generic', ['pathPrefix' => 'http://cakephp.org/assets/css/']);
		$expected['link']['href'] = 'http://cakephp.org/assets/css/cake.generic.css';
		$this->assertTags($result, $expected);

		$previousConfig = Configure::read('App.cssBaseUrl');
		Configure::write('App.cssBaseUrl', '//cdn.cakephp.org/css/');
		$result = $this->Html->css('cake.generic');
		$expected['link']['href'] = '//cdn.cakephp.org/css/cake.generic.css';
		$this->assertTags($result, $expected);
		Configure::write('App.cssBaseUrl', $previousConfig);

		Configure::write('Asset.filter.css', 'css.php');
		$result = $this->Html->css('cake.generic');
		$expected['link']['href'] = 'preg:/.*ccss\/cake\.generic\.css/';
		$this->assertTags($result, $expected);

		$result = $this->Html->css('//example.com/css/cake.generic.css');
		$expected['link']['href'] = 'preg:/.*example\.com\/css\/cake\.generic\.css/';
		$this->assertTags($result, $expected);

		Configure::write('Asset.filter.css', false);

		$result = explode("\n", trim($this->Html->css(['cake.generic', 'vendor.generic'])));
		$expected['link']['href'] = 'preg:/.*css\/cake\.generic\.css/';
		$this->assertTags($result[0], $expected);
		$expected['link']['href'] = 'preg:/.*css\/vendor\.generic\.css/';
		$this->assertTags($result[1], $expected);
		$this->assertEquals(2, count($result));

		$this->View->expects($this->at(0))
			->method('append')
			->with('css', $this->matchesRegularExpression('/css_in_head.css/'));

		$this->View->expects($this->at(1))
			->method('append')
			->with('css', $this->matchesRegularExpression('/more_css_in_head.css/'));

		$result = $this->Html->css('css_in_head', ['inline' => false]);
		$this->assertNull($result);

		$result = $this->Html->css('more_css_in_head', ['inline' => false]);
		$this->assertNull($result);

		$result = $this->Html->css('screen', ['rel' => 'import']);
		$expected = [
			'style' => ['type' => 'text/css'],
			'preg:/@import url\(.*css\/screen\.css\);/',
			'/style'
		];
		$this->assertTags($result, $expected);
	}

/**
 * Test css() with once option.
 *
 * @return void
 */
	public function testCssLinkOnce() {
		Configure::write('Asset.filter.css', false);

		$result = $this->Html->css('screen', ['once' => true]);
		$expected = [
			'link' => ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => 'preg:/.*css\/screen\.css/']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->css('screen', ['once' => true]);
		$this->assertEquals('', $result);

		// Default is once=false
		$result = $this->Html->css('screen');
		$expected = [
			'link' => ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => 'preg:/.*css\/screen\.css/']
		];
		$this->assertTags($result, $expected);
	}

/**
 * Test css link BC usage
 *
 * @return void
 */
	public function testCssLinkBC() {
		Configure::write('Asset.filter.css', false);

		CakePlugin::load('TestPlugin');
		$result = $this->Html->css('TestPlugin.style', null, ['plugin' => false]);
		$expected = [
			'link' => [
				'rel' => 'stylesheet',
				'type' => 'text/css',
				'href' => 'preg:/.*css\/TestPlugin\.style\.css/'
			]
		];
		$this->assertTags($result, $expected);
		CakePlugin::unload('TestPlugin');

		$result = $this->Html->css('screen', 'import');
		$expected = [
			'style' => ['type' => 'text/css'],
			'preg:/@import url\(.*css\/screen\.css\);/',
			'/style'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->css('css_in_head', null, ['inline' => false]);
		$this->assertNull($result);

		$result = $this->Html->css('more_css_in_head', null, ['inline' => false]);
		$this->assertNull($result);
	}

/**
 * testCssWithFullBase method
 *
 * @return void
 */
	public function testCssWithFullBase() {
		Configure::write('Asset.filter.css', false);
		$here = $this->Html->url('/', true);

		$result = $this->Html->css('screen', null, ['fullBase' => true]);
		$expected = [
			'link' => ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => $here . 'css/screen.css']
		];
		$this->assertTags($result, $expected);
	}

/**
 * testPluginCssLink method
 *
 * @return void
 */
	public function testPluginCssLink() {
		Configure::write('Asset.filter.css', false);
		CakePlugin::load('TestPlugin');

		$result = $this->Html->css('TestPlugin.test_plugin_asset');
		$expected = [
			'link' => ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => 'preg:/.*test_plugin\/css\/test_plugin_asset\.css/']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->css('TestPlugin.test_plugin_asset.css');
		$this->assertTags($result, $expected);

		$result = $this->Html->css('TestPlugin.my.css.library');
		$expected['link']['href'] = 'preg:/.*test_plugin\/css\/my\.css\.library\.css/';
		$this->assertTags($result, $expected);

		$result = $this->Html->css('TestPlugin.test_plugin_asset.css?1234');
		$expected['link']['href'] = 'preg:/.*test_plugin\/css\/test_plugin_asset\.css\?1234/';
		$this->assertTags($result, $expected);

		Configure::write('Asset.filter.css', 'css.php');
		$result = $this->Html->css('TestPlugin.test_plugin_asset');
		$expected['link']['href'] = 'preg:/.*test_plugin\/ccss\/test_plugin_asset\.css/';
		$this->assertTags($result, $expected);

		Configure::write('Asset.filter.css', false);

		$result = explode("\n", trim($this->Html->css(['TestPlugin.test_plugin_asset', 'TestPlugin.vendor.generic'])));
		$expected['link']['href'] = 'preg:/.*test_plugin\/css\/test_plugin_asset\.css/';
		$this->assertTags($result[0], $expected);
		$expected['link']['href'] = 'preg:/.*test_plugin\/css\/vendor\.generic\.css/';
		$this->assertTags($result[1], $expected);
		$this->assertEquals(2, count($result));

		CakePlugin::unload('TestPlugin');
	}

/**
 * test use of css() and timestamping
 *
 * @return void
 */
	public function testCssTimestamping() {
		Configure::write('debug', 2);
		Configure::write('Asset.timestamp', true);

		$expected = [
			'link' => ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => '']
		];

		$result = $this->Html->css('cake.generic');
		$expected['link']['href'] = 'preg:/.*css\/cake\.generic\.css\?[0-9]+/';
		$this->assertTags($result, $expected);

		Configure::write('debug', 0);

		$result = $this->Html->css('cake.generic');
		$expected['link']['href'] = 'preg:/.*css\/cake\.generic\.css/';
		$this->assertTags($result, $expected);

		Configure::write('Asset.timestamp', 'force');

		$result = $this->Html->css('cake.generic');
		$expected['link']['href'] = 'preg:/.*css\/cake\.generic\.css\?[0-9]+/';
		$this->assertTags($result, $expected);

		$this->Html->request->webroot = '/testing/';
		$result = $this->Html->css('cake.generic');
		$expected['link']['href'] = 'preg:/\/testing\/css\/cake\.generic\.css\?[0-9]+/';
		$this->assertTags($result, $expected);

		$this->Html->request->webroot = '/testing/longer/';
		$result = $this->Html->css('cake.generic');
		$expected['link']['href'] = 'preg:/\/testing\/longer\/css\/cake\.generic\.css\?[0-9]+/';
		$this->assertTags($result, $expected);
	}

/**
 * test use of css() and timestamping with plugin syntax
 *
 * @return void
 */
	public function testPluginCssTimestamping() {
		CakePlugin::load('TestPlugin');

		Configure::write('debug', 2);
		Configure::write('Asset.timestamp', true);

		$expected = [
			'link' => ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => '']
		];

		$result = $this->Html->css('TestPlugin.test_plugin_asset');
		$expected['link']['href'] = 'preg:/.*test_plugin\/css\/test_plugin_asset\.css\?[0-9]+/';
		$this->assertTags($result, $expected);

		Configure::write('debug', 0);

		$result = $this->Html->css('TestPlugin.test_plugin_asset');
		$expected['link']['href'] = 'preg:/.*test_plugin\/css\/test_plugin_asset\.css/';
		$this->assertTags($result, $expected);

		Configure::write('Asset.timestamp', 'force');

		$result = $this->Html->css('TestPlugin.test_plugin_asset');
		$expected['link']['href'] = 'preg:/.*test_plugin\/css\/test_plugin_asset\.css\?[0-9]+/';
		$this->assertTags($result, $expected);

		$this->Html->request->webroot = '/testing/';
		$result = $this->Html->css('TestPlugin.test_plugin_asset');
		$expected['link']['href'] = 'preg:/\/testing\/test_plugin\/css\/test_plugin_asset\.css\?[0-9]+/';
		$this->assertTags($result, $expected);

		$this->Html->request->webroot = '/testing/longer/';
		$result = $this->Html->css('TestPlugin.test_plugin_asset');
		$expected['link']['href'] = 'preg:/\/testing\/longer\/test_plugin\/css\/test_plugin_asset\.css\?[0-9]+/';
		$this->assertTags($result, $expected);

		CakePlugin::unload('TestPlugin');
	}

/**
 * Resource names must be treated differently for css() and script()
 *
 * @return void
 */
	public function testBufferedCssAndScriptWithIdenticalResourceName() {
		$this->View->expects($this->at(0))
			->method('append')
			->with('css', $this->stringContains('test.min.css'));
		$this->View->expects($this->at(1))
			->method('append')
			->with('script', $this->stringContains('test.min.js'));
		$this->Html->css('test.min', ['inline' => false]);
		$this->Html->script('test.min', ['inline' => false]);
	}

/**
 * test timestamp enforcement for script tags.
 *
 * @return void
 */
	public function testScriptTimestamping() {
		$this->skipIf(!is_writable(WWW_ROOT . 'js'), 'webroot/js is not Writable, timestamp testing has been skipped.');

		Configure::write('debug', 2);
		Configure::write('Asset.timestamp', true);

		touch(WWW_ROOT . 'js' . DS . '__cake_js_test.js');
		$timestamp = substr(strtotime('now'), 0, 8);

		$result = $this->Html->script('__cake_js_test', ['inline' => true, 'once' => false]);
		$this->assertRegExp('/__cake_js_test.js\?' . $timestamp . '[0-9]{2}"/', $result, 'Timestamp value not found %s');

		Configure::write('debug', 0);
		Configure::write('Asset.timestamp', 'force');
		$result = $this->Html->script('__cake_js_test', ['inline' => true, 'once' => false]);
		$this->assertRegExp('/__cake_js_test.js\?' . $timestamp . '[0-9]{2}"/', $result, 'Timestamp value not found %s');
		unlink(WWW_ROOT . 'js' . DS . '__cake_js_test.js');
		Configure::write('Asset.timestamp', false);
	}

/**
 * test timestamp enforcement for script tags with plugin syntax.
 *
 * @return void
 */
	public function testPluginScriptTimestamping() {
		CakePlugin::load('TestPlugin');

		$pluginPath = CakePlugin::path('TestPlugin');
		$pluginJsPath = $pluginPath . 'webroot/js';
		$this->skipIf(!is_writable($pluginJsPath), $pluginJsPath . ' is not Writable, timestamp testing has been skipped.');

		Configure::write('debug', 2);
		Configure::write('Asset.timestamp', true);

		touch($pluginJsPath . DS . '__cake_js_test.js');
		$timestamp = substr(strtotime('now'), 0, 8);

		$result = $this->Html->script('TestPlugin.__cake_js_test', ['inline' => true, 'once' => false]);
		$this->assertRegExp('/test_plugin\/js\/__cake_js_test.js\?' . $timestamp . '[0-9]{2}"/', $result, 'Timestamp value not found %s');

		Configure::write('debug', 0);
		Configure::write('Asset.timestamp', 'force');
		$result = $this->Html->script('TestPlugin.__cake_js_test', ['inline' => true, 'once' => false]);
		$this->assertRegExp('/test_plugin\/js\/__cake_js_test.js\?' . $timestamp . '[0-9]{2}"/', $result, 'Timestamp value not found %s');
		unlink($pluginJsPath . DS . '__cake_js_test.js');
		Configure::write('Asset.timestamp', false);

		CakePlugin::unload('TestPlugin');
	}

/**
 * test that scripts added with uses() are only ever included once.
 * test script tag generation
 *
 * @return void
 */
	public function testScript() {
		$result = $this->Html->script('foo');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'js/foo.js']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script(['foobar', 'bar']);
		$expected = [
			['script' => ['type' => 'text/javascript', 'src' => 'js/foobar.js']],
			'/script',
			['script' => ['type' => 'text/javascript', 'src' => 'js/bar.js']],
			'/script',
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('jquery-1.3');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'js/jquery-1.3.js']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('test.json');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'js/test.json.js']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('http://example.com/test.json');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'http://example.com/test.json']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('/plugin/js/jquery-1.3.2.js?someparam=foo');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => '/plugin/js/jquery-1.3.2.js?someparam=foo']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('test.json.js?foo=bar');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'js/test.json.js?foo=bar']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('test.json.js?foo=bar&other=test');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'js/test.json.js?foo=bar&amp;other=test']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('foo2', ['pathPrefix' => '/my/custom/path/']);
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => '/my/custom/path/foo2.js']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('foo3', ['pathPrefix' => 'http://cakephp.org/assets/js/']);
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'http://cakephp.org/assets/js/foo3.js']
		];
		$this->assertTags($result, $expected);

		$previousConfig = Configure::read('App.jsBaseUrl');
		Configure::write('App.jsBaseUrl', '//cdn.cakephp.org/js/');
		$result = $this->Html->script('foo4');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => '//cdn.cakephp.org/js/foo4.js']
		];
		$this->assertTags($result, $expected);
		Configure::write('App.jsBaseUrl', $previousConfig);

		$result = $this->Html->script('foo');
		$this->assertNull($result, 'Script returned upon duplicate inclusion %s');

		$result = $this->Html->script(['foo', 'bar', 'baz']);
		$this->assertNotRegExp('/foo.js/', $result);

		$result = $this->Html->script('foo', ['inline' => true, 'once' => false]);
		$this->assertNotNull($result);

		$result = $this->Html->script('jquery-1.3.2', ['defer' => true, 'encoding' => 'utf-8']);
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'js/jquery-1.3.2.js', 'defer' => 'defer', 'encoding' => 'utf-8']
		];
		$this->assertTags($result, $expected);
	}

/**
 * test that plugin scripts added with uses() are only ever included once.
 * test script tag generation with plugin syntax
 *
 * @return void
 */
	public function testPluginScript() {
		CakePlugin::load('TestPlugin');

		$result = $this->Html->script('TestPlugin.foo');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'test_plugin/js/foo.js']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script(['TestPlugin.foobar', 'TestPlugin.bar']);
		$expected = [
			['script' => ['type' => 'text/javascript', 'src' => 'test_plugin/js/foobar.js']],
			'/script',
			['script' => ['type' => 'text/javascript', 'src' => 'test_plugin/js/bar.js']],
			'/script',
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('TestPlugin.jquery-1.3');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'test_plugin/js/jquery-1.3.js']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('TestPlugin.test.json');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'test_plugin/js/test.json.js']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('TestPlugin./jquery-1.3.2.js?someparam=foo');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'test_plugin/jquery-1.3.2.js?someparam=foo']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('TestPlugin.test.json.js?foo=bar');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'test_plugin/js/test.json.js?foo=bar']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('TestPlugin.foo');
		$this->assertNull($result, 'Script returned upon duplicate inclusion %s');

		$result = $this->Html->script(['TestPlugin.foo', 'TestPlugin.bar', 'TestPlugin.baz']);
		$this->assertNotRegExp('/test_plugin\/js\/foo.js/', $result);

		$result = $this->Html->script('TestPlugin.foo', ['inline' => true, 'once' => false]);
		$this->assertNotNull($result);

		$result = $this->Html->script('TestPlugin.jquery-1.3.2', ['defer' => true, 'encoding' => 'utf-8']);
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'test_plugin/js/jquery-1.3.2.js', 'defer' => 'defer', 'encoding' => 'utf-8']
		];
		$this->assertTags($result, $expected);

		CakePlugin::unload('TestPlugin');
	}

/**
 * test that script() works with blocks.
 *
 * @return void
 */
	public function testScriptWithBlocks() {
		$this->View->expects($this->at(0))
			->method('append')
			->with('script', $this->matchesRegularExpression('/script_in_head.js/'));

		$this->View->expects($this->at(1))
			->method('append')
			->with('script', $this->matchesRegularExpression('/bool_false.js/'));

		$this->View->expects($this->at(2))
			->method('append')
			->with('headScripts', $this->matchesRegularExpression('/second_script.js/'));

		$result = $this->Html->script('script_in_head', ['inline' => false]);
		$this->assertNull($result);

		$result = $this->Html->script('bool_false', false);
		$this->assertNull($result);

		$result = $this->Html->script('second_script', ['block' => 'headScripts']);
		$this->assertNull($result);
	}

/**
 * Test that Asset.filter.js works.
 *
 * @return void
 */
	public function testScriptAssetFilter() {
		Configure::write('Asset.filter.js', 'js.php');

		$result = $this->Html->script('jquery-1.3');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => 'cjs/jquery-1.3.js']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script('//example.com/js/jquery-1.3.js');
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => '//example.com/js/jquery-1.3.js']
		];
		$this->assertTags($result, $expected);
	}

/**
 * testScriptWithFullBase method
 *
 * @return void
 */
	public function testScriptWithFullBase() {
		$here = $this->Html->url('/', true);

		$result = $this->Html->script('foo', ['fullBase' => true]);
		$expected = [
			'script' => ['type' => 'text/javascript', 'src' => $here . 'js/foo.js']
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->script(['foobar', 'bar'], ['fullBase' => true]);
		$expected = [
			['script' => ['type' => 'text/javascript', 'src' => $here . 'js/foobar.js']],
			'/script',
			['script' => ['type' => 'text/javascript', 'src' => $here . 'js/bar.js']],
			'/script',
		];
		$this->assertTags($result, $expected);
	}

/**
 * test a script file in the webroot/theme dir.
 *
 * @return void
 */
	public function testScriptInTheme() {
		$this->skipIf(!is_writable(WWW_ROOT), 'Cannot write to webroot.');
		$themeExists = is_dir(WWW_ROOT . 'theme');

		App::uses('File', 'Utility');

		$testfile = WWW_ROOT . 'theme' . DS . 'test_theme' . DS . 'js' . DS . '__test_js.js';
		new File($testfile, true);

		App::build([
			'View' => [CAKE . 'Test' . DS . 'test_app' . DS . 'View' . DS]
		]);

		$this->Html->webroot = '/';
		$this->Html->theme = 'test_theme';
		$result = $this->Html->script('__test_js.js');
		$expected = [
			'script' => ['src' => '/theme/test_theme/js/__test_js.js', 'type' => 'text/javascript']
		];
		$this->assertTags($result, $expected);

		$Folder = new Folder(WWW_ROOT . 'theme' . DS . 'test_theme');
		$Folder->delete();

		if (!$themeExists) {
			$dir = new Folder(WWW_ROOT . 'theme');
			$dir->delete();
		}
	}

/**
 * test Script block generation
 *
 * @return void
 */
	public function testScriptBlock() {
		$result = $this->Html->scriptBlock('window.foo = 2;');
		$expected = [
			'script' => ['type' => 'text/javascript'],
			$this->cDataStart,
			'window.foo = 2;',
			$this->cDataEnd,
			'/script',
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->scriptBlock('window.foo = 2;', ['type' => 'text/x-handlebars-template']);
		$expected = [
			'script' => ['type' => 'text/x-handlebars-template'],
			$this->cDataStart,
			'window.foo = 2;',
			$this->cDataEnd,
			'/script',
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->scriptBlock('window.foo = 2;', ['safe' => false]);
		$expected = [
			'script' => ['type' => 'text/javascript'],
			'window.foo = 2;',
			'/script',
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->scriptBlock('window.foo = 2;', ['safe' => true]);
		$expected = [
			'script' => ['type' => 'text/javascript'],
			$this->cDataStart,
			'window.foo = 2;',
			$this->cDataEnd,
			'/script',
		];
		$this->assertTags($result, $expected);

		$this->View->expects($this->at(0))
			->method('append')
			->with('script', $this->matchesRegularExpression('/window\.foo\s\=\s2;/'));

		$this->View->expects($this->at(1))
			->method('append')
			->with('scriptTop', $this->stringContains('alert('));

		$result = $this->Html->scriptBlock('window.foo = 2;', ['inline' => false]);
		$this->assertNull($result);

		$result = $this->Html->scriptBlock('alert("hi")', ['block' => 'scriptTop']);
		$this->assertNull($result);

		$result = $this->Html->scriptBlock('window.foo = 2;', ['safe' => false, 'encoding' => 'utf-8']);
		$expected = [
			'script' => ['type' => 'text/javascript', 'encoding' => 'utf-8'],
			'window.foo = 2;',
			'/script',
		];
		$this->assertTags($result, $expected);
	}

/**
 * test script tag output buffering when using scriptStart() and scriptEnd();
 *
 * @return void
 */
	public function testScriptStartAndScriptEnd() {
		$result = $this->Html->scriptStart(['safe' => true]);
		$this->assertNull($result);
		echo 'this is some javascript';

		$result = $this->Html->scriptEnd();
		$expected = [
			'script' => ['type' => 'text/javascript'],
			$this->cDataStart,
			'this is some javascript',
			$this->cDataEnd,
			'/script'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->scriptStart(['safe' => false]);
		$this->assertNull($result);
		echo 'this is some javascript';

		$result = $this->Html->scriptEnd();
		$expected = [
			'script' => ['type' => 'text/javascript'],
			'this is some javascript',
			'/script'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->scriptStart(['safe' => true, 'type' => 'text/x-handlebars-template']);
		$this->assertNull($result);
		echo 'this is some template';

		$result = $this->Html->scriptEnd();
		$expected = [
			'script' => ['type' => 'text/x-handlebars-template'],
			$this->cDataStart,
			'this is some template',
			$this->cDataEnd,
			'/script'
		];
		$this->assertTags($result, $expected);

		$this->View->expects($this->once())
			->method('append');
		$result = $this->Html->scriptStart(['safe' => false, 'inline' => false]);
		$this->assertNull($result);
		echo 'this is some javascript';

		$result = $this->Html->scriptEnd();
		$this->assertNull($result);
	}

/**
 * testCharsetTag method
 *
 * @return void
 */
	public function testCharsetTag() {
		Configure::write('App.encoding', null);
		$result = $this->Html->charset();
		$this->assertTags($result, ['meta' => ['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=utf-8']]);

		Configure::write('App.encoding', 'ISO-8859-1');
		$result = $this->Html->charset();
		$this->assertTags($result, ['meta' => ['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=iso-8859-1']]);

		$result = $this->Html->charset('UTF-7');
		$this->assertTags($result, ['meta' => ['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=UTF-7']]);
	}

/**
 * testGetCrumb and addCrumb method
 *
 * @return void
 */
	public function testBreadcrumb() {
		$this->assertNull($this->Html->getCrumbs());

		$this->Html->addCrumb('First', '#first');
		$this->Html->addCrumb('Second', '#second');
		$this->Html->addCrumb('Third', '#third');

		$result = $this->Html->getCrumbs();
		$expected = [
			['a' => ['href' => '#first']],
			'First',
			'/a',
			'&raquo;',
			['a' => ['href' => '#second']],
			'Second',
			'/a',
			'&raquo;',
			['a' => ['href' => '#third']],
			'Third',
			'/a',
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->getCrumbs(' &gt; ');
		$expected = [
			['a' => ['href' => '#first']],
			'First',
			'/a',
			' &gt; ',
			['a' => ['href' => '#second']],
			'Second',
			'/a',
			' &gt; ',
			['a' => ['href' => '#third']],
			'Third',
			'/a',
		];
		$this->assertTags($result, $expected);

		$this->Html->addCrumb('Fourth', null);

		$result = $this->Html->getCrumbs();
		$expected = [
			['a' => ['href' => '#first']],
			'First',
			'/a',
			'&raquo;',
			['a' => ['href' => '#second']],
			'Second',
			'/a',
			'&raquo;',
			['a' => ['href' => '#third']],
			'Third',
			'/a',
			'&raquo;',
			'Fourth'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->getCrumbs('-', 'Start');
		$expected = [
			['a' => ['href' => '/']],
			'Start',
			'/a',
			'-',
			['a' => ['href' => '#first']],
			'First',
			'/a',
			'-',
			['a' => ['href' => '#second']],
			'Second',
			'/a',
			'-',
			['a' => ['href' => '#third']],
			'Third',
			'/a',
			'-',
			'Fourth'
		];
		$this->assertTags($result, $expected);
	}

/**
 * Test the array form of $startText
 *
 * @return void
 */
	public function testGetCrumbFirstLink() {
		$result = $this->Html->getCrumbList(null, 'Home');
		$this->assertTags(
			$result,
			[
				'<ul',
				['li' => ['class' => 'first']],
				['a' => ['href' => '/']], 'Home', '/a',
				'/li',
				'/ul'
			]
		);

		$this->Html->addCrumb('First', '#first');
		$this->Html->addCrumb('Second', '#second');

		$result = $this->Html->getCrumbs(' - ', ['url' => '/home', 'text' => '<img src="/home.png" />', 'escape' => false]);
		$expected = [
			['a' => ['href' => '/home']],
			'img' => ['src' => '/home.png'],
			'/a',
			' - ',
			['a' => ['href' => '#first']],
			'First',
			'/a',
			' - ',
			['a' => ['href' => '#second']],
			'Second',
			'/a',
		];
		$this->assertTags($result, $expected);
	}

/**
 * testNestedList method
 *
 * @return void
 */
	public function testNestedList() {
		$list = [
			'Item 1',
			'Item 2' => [
				'Item 2.1'
			],
			'Item 3',
			'Item 4' => [
				'Item 4.1',
				'Item 4.2',
				'Item 4.3' => [
					'Item 4.3.1',
					'Item 4.3.2'
				]
			],
			'Item 5' => [
				'Item 5.1',
				'Item 5.2'
			]
		];

		$result = $this->Html->nestedList($list);
		$expected = [
			'<ul',
			'<li', 'Item 1', '/li',
			'<li', 'Item 2',
			'<ul', '<li', 'Item 2.1', '/li', '/ul',
			'/li',
			'<li', 'Item 3', '/li',
			'<li', 'Item 4',
			'<ul',
			'<li', 'Item 4.1', '/li',
			'<li', 'Item 4.2', '/li',
			'<li', 'Item 4.3',
			'<ul',
			'<li', 'Item 4.3.1', '/li',
			'<li', 'Item 4.3.2', '/li',
			'/ul',
			'/li',
			'/ul',
			'/li',
			'<li', 'Item 5',
			'<ul',
			'<li', 'Item 5.1', '/li',
			'<li', 'Item 5.2', '/li',
			'/ul',
			'/li',
			'/ul'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->nestedList($list, null);
		$this->assertTags($result, $expected);

		$result = $this->Html->nestedList($list, [], [], 'ol');
		$expected = [
			'<ol',
			'<li', 'Item 1', '/li',
			'<li', 'Item 2',
			'<ol', '<li', 'Item 2.1', '/li', '/ol',
			'/li',
			'<li', 'Item 3', '/li',
			'<li', 'Item 4',
			'<ol',
			'<li', 'Item 4.1', '/li',
			'<li', 'Item 4.2', '/li',
			'<li', 'Item 4.3',
			'<ol',
			'<li', 'Item 4.3.1', '/li',
			'<li', 'Item 4.3.2', '/li',
			'/ol',
			'/li',
			'/ol',
			'/li',
			'<li', 'Item 5',
			'<ol',
			'<li', 'Item 5.1', '/li',
			'<li', 'Item 5.2', '/li',
			'/ol',
			'/li',
			'/ol'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->nestedList($list, 'ol');
		$this->assertTags($result, $expected);

		$result = $this->Html->nestedList($list, ['class' => 'list']);
		$expected = [
			['ul' => ['class' => 'list']],
			'<li', 'Item 1', '/li',
			'<li', 'Item 2',
			['ul' => ['class' => 'list']], '<li', 'Item 2.1', '/li', '/ul',
			'/li',
			'<li', 'Item 3', '/li',
			'<li', 'Item 4',
			['ul' => ['class' => 'list']],
			'<li', 'Item 4.1', '/li',
			'<li', 'Item 4.2', '/li',
			'<li', 'Item 4.3',
			['ul' => ['class' => 'list']],
			'<li', 'Item 4.3.1', '/li',
			'<li', 'Item 4.3.2', '/li',
			'/ul',
			'/li',
			'/ul',
			'/li',
			'<li', 'Item 5',
			['ul' => ['class' => 'list']],
			'<li', 'Item 5.1', '/li',
			'<li', 'Item 5.2', '/li',
			'/ul',
			'/li',
			'/ul'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->nestedList($list, [], ['class' => 'item']);
		$expected = [
			'<ul',
			['li' => ['class' => 'item']], 'Item 1', '/li',
			['li' => ['class' => 'item']], 'Item 2',
			'<ul', ['li' => ['class' => 'item']], 'Item 2.1', '/li', '/ul',
			'/li',
			['li' => ['class' => 'item']], 'Item 3', '/li',
			['li' => ['class' => 'item']], 'Item 4',
			'<ul',
			['li' => ['class' => 'item']], 'Item 4.1', '/li',
			['li' => ['class' => 'item']], 'Item 4.2', '/li',
			['li' => ['class' => 'item']], 'Item 4.3',
			'<ul',
			['li' => ['class' => 'item']], 'Item 4.3.1', '/li',
			['li' => ['class' => 'item']], 'Item 4.3.2', '/li',
			'/ul',
			'/li',
			'/ul',
			'/li',
			['li' => ['class' => 'item']], 'Item 5',
			'<ul',
			['li' => ['class' => 'item']], 'Item 5.1', '/li',
			['li' => ['class' => 'item']], 'Item 5.2', '/li',
			'/ul',
			'/li',
			'/ul'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->nestedList($list, [], ['even' => 'even', 'odd' => 'odd']);
		$expected = [
			'<ul',
			['li' => ['class' => 'odd']], 'Item 1', '/li',
			['li' => ['class' => 'even']], 'Item 2',
			'<ul', ['li' => ['class' => 'odd']], 'Item 2.1', '/li', '/ul',
			'/li',
			['li' => ['class' => 'odd']], 'Item 3', '/li',
			['li' => ['class' => 'even']], 'Item 4',
			'<ul',
			['li' => ['class' => 'odd']], 'Item 4.1', '/li',
			['li' => ['class' => 'even']], 'Item 4.2', '/li',
			['li' => ['class' => 'odd']], 'Item 4.3',
			'<ul',
			['li' => ['class' => 'odd']], 'Item 4.3.1', '/li',
			['li' => ['class' => 'even']], 'Item 4.3.2', '/li',
			'/ul',
			'/li',
			'/ul',
			'/li',
			['li' => ['class' => 'odd']], 'Item 5',
			'<ul',
			['li' => ['class' => 'odd']], 'Item 5.1', '/li',
			['li' => ['class' => 'even']], 'Item 5.2', '/li',
			'/ul',
			'/li',
			'/ul'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->nestedList($list, ['class' => 'list'], ['class' => 'item']);
		$expected = [
			['ul' => ['class' => 'list']],
			['li' => ['class' => 'item']], 'Item 1', '/li',
			['li' => ['class' => 'item']], 'Item 2',
			['ul' => ['class' => 'list']], ['li' => ['class' => 'item']], 'Item 2.1', '/li', '/ul',
			'/li',
			['li' => ['class' => 'item']], 'Item 3', '/li',
			['li' => ['class' => 'item']], 'Item 4',
			['ul' => ['class' => 'list']],
			['li' => ['class' => 'item']], 'Item 4.1', '/li',
			['li' => ['class' => 'item']], 'Item 4.2', '/li',
			['li' => ['class' => 'item']], 'Item 4.3',
			['ul' => ['class' => 'list']],
			['li' => ['class' => 'item']], 'Item 4.3.1', '/li',
			['li' => ['class' => 'item']], 'Item 4.3.2', '/li',
			'/ul',
			'/li',
			'/ul',
			'/li',
			['li' => ['class' => 'item']], 'Item 5',
			['ul' => ['class' => 'list']],
			['li' => ['class' => 'item']], 'Item 5.1', '/li',
			['li' => ['class' => 'item']], 'Item 5.2', '/li',
			'/ul',
			'/li',
			'/ul'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testMeta method
 *
 * @return void
 */
	public function testMeta() {
		$result = $this->Html->meta('this is an rss feed', ['controller' => 'posts', 'ext' => 'rss']);
		$this->assertTags($result, ['link' => ['href' => 'preg:/.*\/posts\.rss/', 'type' => 'application/rss+xml', 'rel' => 'alternate', 'title' => 'this is an rss feed']]);

		$result = $this->Html->meta('rss', ['controller' => 'posts', 'ext' => 'rss'], ['title' => 'this is an rss feed']);
		$this->assertTags($result, ['link' => ['href' => 'preg:/.*\/posts\.rss/', 'type' => 'application/rss+xml', 'rel' => 'alternate', 'title' => 'this is an rss feed']]);

		$result = $this->Html->meta('atom', ['controller' => 'posts', 'ext' => 'xml']);
		$this->assertTags($result, ['link' => ['href' => 'preg:/.*\/posts\.xml/', 'type' => 'application/atom+xml', 'title' => 'atom']]);

		$result = $this->Html->meta('non-existing');
		$this->assertTags($result, ['<meta']);

		$result = $this->Html->meta('non-existing', '/posts.xpp');
		$this->assertTags($result, ['link' => ['href' => 'preg:/.*\/posts\.xpp/', 'type' => 'application/rss+xml', 'rel' => 'alternate', 'title' => 'non-existing']]);

		$result = $this->Html->meta('non-existing', '/posts.xpp', ['type' => 'atom']);
		$this->assertTags($result, ['link' => ['href' => 'preg:/.*\/posts\.xpp/', 'type' => 'application/atom+xml', 'title' => 'non-existing']]);

		$result = $this->Html->meta('atom', ['controller' => 'posts', 'ext' => 'xml'], ['link' => '/articles.rss']);
		$this->assertTags($result, ['link' => ['href' => 'preg:/.*\/articles\.rss/', 'type' => 'application/atom+xml', 'title' => 'atom']]);

		$result = $this->Html->meta(['link' => 'favicon.ico', 'rel' => 'icon']);
		$expected = [
			'link' => ['href' => 'preg:/.*favicon\.ico/', 'rel' => 'icon'],
			['link' => ['href' => 'preg:/.*favicon\.ico/', 'rel' => 'shortcut icon']]
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->meta('keywords', 'these, are, some, meta, keywords');
		$this->assertTags($result, ['meta' => ['name' => 'keywords', 'content' => 'these, are, some, meta, keywords']]);

		$result = $this->Html->meta('description', 'this is the meta description');
		$this->assertTags($result, ['meta' => ['name' => 'description', 'content' => 'this is the meta description']]);

		$result = $this->Html->meta(['name' => 'ROBOTS', 'content' => 'ALL']);
		$this->assertTags($result, ['meta' => ['name' => 'ROBOTS', 'content' => 'ALL']]);
	}

/**
 * Test generating favicon's with meta()
 *
 * @return void
 */
	public function testMetaIcon() {
		$result = $this->Html->meta('icon', 'favicon.ico');
		$expected = [
			'link' => ['href' => 'preg:/.*favicon\.ico/', 'type' => 'image/x-icon', 'rel' => 'icon'],
			['link' => ['href' => 'preg:/.*favicon\.ico/', 'type' => 'image/x-icon', 'rel' => 'shortcut icon']]
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->meta('icon');
		$expected = [
			'link' => ['href' => 'preg:/.*favicon\.ico/', 'type' => 'image/x-icon', 'rel' => 'icon'],
			['link' => ['href' => 'preg:/.*favicon\.ico/', 'type' => 'image/x-icon', 'rel' => 'shortcut icon']]
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->meta('icon', '/favicon.png?one=two&three=four');
		$url = '/favicon.png?one=two&amp;three=four';
		$expected = [
			'link' => [
				'href' => $url,
				'type' => 'image/x-icon',
				'rel' => 'icon'
			],
			[
				'link' => [
					'href' => $url,
					'type' => 'image/x-icon',
					'rel' => 'shortcut icon'
				]
			]
		];
		$this->assertTags($result, $expected);

		$this->Html->request->webroot = '/testing/';
		$result = $this->Html->meta('icon');
		$expected = [
			'link' => ['href' => '/testing/favicon.ico', 'type' => 'image/x-icon', 'rel' => 'icon'],
			['link' => ['href' => '/testing/favicon.ico', 'type' => 'image/x-icon', 'rel' => 'shortcut icon']]
		];
		$this->assertTags($result, $expected);
	}

/**
 * Test the inline and block options for meta()
 *
 * @return void
 */
	public function testMetaWithBlocks() {
		$this->View->expects($this->at(0))
			->method('append')
			->with('meta', $this->stringContains('ROBOTS'));

		$this->View->expects($this->at(1))
			->method('append')
			->with('metaTags', $this->stringContains('favicon.ico'));

		$result = $this->Html->meta(['name' => 'ROBOTS', 'content' => 'ALL'], null, ['inline' => false]);
		$this->assertNull($result);

		$result = $this->Html->meta('icon', 'favicon.ico', ['block' => 'metaTags']);
		$this->assertNull($result);
	}

/**
 * testTableHeaders method
 *
 * @return void
 */
	public function testTableHeaders() {
		$result = $this->Html->tableHeaders(['ID', 'Name', 'Date']);
		$expected = ['<tr', '<th', 'ID', '/th', '<th', 'Name', '/th', '<th', 'Date', '/th', '/tr'];
		$this->assertTags($result, $expected);

		$result = $this->Html->tableHeaders(['ID', ['Name' => ['class' => 'highlight']], 'Date']);
		$expected = ['<tr', '<th', 'ID', '/th', '<th class="highlight"', 'Name', '/th', '<th', 'Date', '/th', '/tr'];
		$this->assertTags($result, $expected);

		$result = $this->Html->tableHeaders(['ID', ['Name' => ['class' => 'highlight', 'width' => '120px']], 'Date']);
		$expected = ['<tr', '<th', 'ID', '/th', '<th class="highlight" width="120px"', 'Name', '/th', '<th', 'Date', '/th', '/tr'];
		$this->assertTags($result, $expected);

		$result = $this->Html->tableHeaders(['ID', ['Name' => []], 'Date']);
		$expected = ['<tr', '<th', 'ID', '/th', '<th', 'Name', '/th', '<th', 'Date', '/th', '/tr'];
		$this->assertTags($result, $expected);
	}

/**
 * testTableCells method
 *
 * @return void
 */
	public function testTableCells() {
		$tr = [
			'td content 1',
			['td content 2', ["width" => "100px"]],
			['td content 3', "width=100px"]
		];
		$result = $this->Html->tableCells($tr);
		$expected = [
			'<tr',
			'<td', 'td content 1', '/td',
			['td' => ['width' => '100px']], 'td content 2', '/td',
			['td' => ['width' => 'preg:/100px/']], 'td content 3', '/td',
			'/tr'
		];
		$this->assertTags($result, $expected);

		$tr = ['td content 1', 'td content 2', 'td content 3'];
		$result = $this->Html->tableCells($tr, null, null, true);
		$expected = [
			'<tr',
			['td' => ['class' => 'column-1']], 'td content 1', '/td',
			['td' => ['class' => 'column-2']], 'td content 2', '/td',
			['td' => ['class' => 'column-3']], 'td content 3', '/td',
			'/tr'
		];
		$this->assertTags($result, $expected);

		$tr = ['td content 1', 'td content 2', 'td content 3'];
		$result = $this->Html->tableCells($tr, true);
		$expected = [
			'<tr',
			['td' => ['class' => 'column-1']], 'td content 1', '/td',
			['td' => ['class' => 'column-2']], 'td content 2', '/td',
			['td' => ['class' => 'column-3']], 'td content 3', '/td',
			'/tr'
		];
		$this->assertTags($result, $expected);

		$tr = [
			['td content 1', 'td content 2', 'td content 3'],
			['td content 1', 'td content 2', 'td content 3'],
			['td content 1', 'td content 2', 'td content 3']
		];
		$result = $this->Html->tableCells($tr, ['class' => 'odd'], ['class' => 'even']);
		$expected = "<tr class=\"even\"><td>td content 1</td> <td>td content 2</td> <td>td content 3</td></tr>\n<tr class=\"odd\"><td>td content 1</td> <td>td content 2</td> <td>td content 3</td></tr>\n<tr class=\"even\"><td>td content 1</td> <td>td content 2</td> <td>td content 3</td></tr>";
		$this->assertEquals($expected, $result);

		$tr = [
			['td content 1', 'td content 2', 'td content 3'],
			['td content 1', 'td content 2', 'td content 3'],
			['td content 1', 'td content 2', 'td content 3'],
			['td content 1', 'td content 2', 'td content 3']
		];
		$result = $this->Html->tableCells($tr, ['class' => 'odd'], ['class' => 'even']);
		$expected = "<tr class=\"odd\"><td>td content 1</td> <td>td content 2</td> <td>td content 3</td></tr>\n<tr class=\"even\"><td>td content 1</td> <td>td content 2</td> <td>td content 3</td></tr>\n<tr class=\"odd\"><td>td content 1</td> <td>td content 2</td> <td>td content 3</td></tr>\n<tr class=\"even\"><td>td content 1</td> <td>td content 2</td> <td>td content 3</td></tr>";
		$this->assertEquals($expected, $result);

		$tr = [
			['td content 1', 'td content 2', 'td content 3'],
			['td content 1', 'td content 2', 'td content 3'],
			['td content 1', 'td content 2', 'td content 3']
		];
		$this->Html->tableCells($tr, ['class' => 'odd'], ['class' => 'even']);
		$result = $this->Html->tableCells($tr, ['class' => 'odd'], ['class' => 'even'], false, false);
		$expected = "<tr class=\"odd\"><td>td content 1</td> <td>td content 2</td> <td>td content 3</td></tr>\n<tr class=\"even\"><td>td content 1</td> <td>td content 2</td> <td>td content 3</td></tr>\n<tr class=\"odd\"><td>td content 1</td> <td>td content 2</td> <td>td content 3</td></tr>";
		$this->assertEquals($expected, $result);

		$tr = [
			'td content 1',
			'td content 2',
			['td content 3', ['class' => 'foo']]
		];
		$result = $this->Html->tableCells($tr, null, null, true);
		$expected = [
			'<tr',
			['td' => ['class' => 'column-1']], 'td content 1', '/td',
			['td' => ['class' => 'column-2']], 'td content 2', '/td',
			['td' => ['class' => 'foo column-3']], 'td content 3', '/td',
			'/tr'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testTag method
 *
 * @return void
 */
	public function testTag() {
		$result = $this->Html->tag('div');
		$this->assertTags($result, '<div');

		$result = $this->Html->tag('div', 'text');
		$this->assertTags($result, '<div', 'text', '/div');

		$result = $this->Html->tag('div', '<text>', ['class' => 'class-name', 'escape' => true]);
		$this->assertTags($result, ['div' => ['class' => 'class-name'], '&lt;text&gt;', '/div']);

		$result = $this->Html->tag(false, '<em>stuff</em>');
		$this->assertEquals('<em>stuff</em>', $result);

		$result = $this->Html->tag(null, '<em>stuff</em>');
		$this->assertEquals('<em>stuff</em>', $result);

		$result = $this->Html->tag('', '<em>stuff</em>');
		$this->assertEquals('<em>stuff</em>', $result);
	}

/**
 * testUseTag method
 *
 * @return void
 */
	public function testUseTag() {
		$result = $this->Html->useTag('unknowntag');
		$this->assertEquals('', $result);

		$result = $this->Html->useTag('formend');
		$this->assertTags($result, '/form');

		$result = $this->Html->useTag('form', 'url', ' test');
		$this->assertEquals('<form action="url" test>', $result);

		$result = $this->Html->useTag('form', 'example.com', ['test' => 'ok']);
		$this->assertTags($result, ['form' => ['test' => 'ok', 'action' => 'example.com']]);
	}

/**
 * testDiv method
 *
 * @return void
 */
	public function testDiv() {
		$result = $this->Html->div('class-name');
		$this->assertTags($result, ['div' => ['class' => 'class-name']]);

		$result = $this->Html->div('class-name', 'text');
		$this->assertTags($result, ['div' => ['class' => 'class-name'], 'text', '/div']);

		$result = $this->Html->div('class-name', '<text>', ['escape' => true]);
		$this->assertTags($result, ['div' => ['class' => 'class-name'], '&lt;text&gt;', '/div']);
	}

/**
 * testPara method
 *
 * @return void
 */
	public function testPara() {
		$result = $this->Html->para('class-name', '');
		$this->assertTags($result, ['p' => ['class' => 'class-name']]);

		$result = $this->Html->para('class-name', 'text');
		$this->assertTags($result, ['p' => ['class' => 'class-name'], 'text', '/p']);

		$result = $this->Html->para('class-name', '<text>', ['escape' => true]);
		$this->assertTags($result, ['p' => ['class' => 'class-name'], '&lt;text&gt;', '/p']);
	}

/**
 * testMedia method
 *
 * @return void
 */
	public function testMedia() {
		$result = $this->Html->media('video.webm');
		$expected = ['video' => ['src' => 'files/video.webm'], '/video'];
		$this->assertTags($result, $expected);

		$result = $this->Html->media('video.webm', [
			'text' => 'Your browser does not support the HTML5 Video element.'
		]);
		$expected = ['video' => ['src' => 'files/video.webm'], 'Your browser does not support the HTML5 Video element.', '/video'];
		$this->assertTags($result, $expected);

		$result = $this->Html->media('video.webm', ['autoload', 'muted' => 'muted']);
		$expected = [
			'video' => [
				'src' => 'files/video.webm',
				'autoload' => 'autoload',
				'muted' => 'muted'
			],
			'/video'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->media(
			['video.webm', ['src' => 'video.ogv', 'type' => "video/ogg; codecs='theora, vorbis'"]],
			['pathPrefix' => 'videos/', 'poster' => 'poster.jpg', 'text' => 'Your browser does not support the HTML5 Video element.']
		);
		$expected = [
			'video' => ['poster' => Configure::read('App.imageBaseUrl') . 'poster.jpg'],
				['source' => ['src' => 'videos/video.webm', 'type' => 'video/webm']],
				['source' => ['src' => 'videos/video.ogv', 'type' => 'video/ogg; codecs=&#039;theora, vorbis&#039;']],
				'Your browser does not support the HTML5 Video element.',
			'/video'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->media('video.ogv', ['tag' => 'video']);
		$expected = ['video' => ['src' => 'files/video.ogv'], '/video'];
		$this->assertTags($result, $expected);

		$result = $this->Html->media('audio.mp3');
		$expected = ['audio' => ['src' => 'files/audio.mp3'], '/audio'];
		$this->assertTags($result, $expected);

		$result = $this->Html->media(
			[['src' => 'video.mov', 'type' => 'video/mp4'], 'video.webm']
		);
		$expected = [
			'<video',
				['source' => ['src' => 'files/video.mov', 'type' => 'video/mp4']],
				['source' => ['src' => 'files/video.webm', 'type' => 'video/webm']],
			'/video'
		];
		$this->assertTags($result, $expected);

		$result = $this->Html->media(null, ['src' => 'video.webm']);
		$expected = [
			'video' => ['src' => 'files/video.webm'],
			'/video'
		];
		$this->assertTags($result, $expected);
	}

/**
 * testCrumbList method
 *
 * @return void
 */
	public function testCrumbList() {
		$this->assertNull($this->Html->getCrumbList());

		$this->Html->addCrumb('Home', '/', ['class' => 'home']);
		$this->Html->addCrumb('Some page', '/some_page');
		$this->Html->addCrumb('Another page');
		$result = $this->Html->getCrumbList(
			['class' => 'breadcrumbs']
		);
		$this->assertTags(
			$result,
			[
				['ul' => ['class' => 'breadcrumbs']],
				['li' => ['class' => 'first']],
				['a' => ['class' => 'home', 'href' => '/']], 'Home', '/a',
				'/li',
				'<li',
				['a' => ['href' => '/some_page']], 'Some page', '/a',
				'/li',
				['li' => ['class' => 'last']],
				'Another page',
				'/li',
				'/ul'
			]
		);
	}

/**
 * Test getCrumbList startText
 *
 * @return void
 */
	public function testCrumbListFirstLink() {
		$this->Html->addCrumb('First', '#first');
		$this->Html->addCrumb('Second', '#second');

		$result = $this->Html->getCrumbList(null, 'Home');
		$this->assertTags(
			$result,
			[
				'<ul',
				['li' => ['class' => 'first']],
				['a' => ['href' => '/']], 'Home', '/a',
				'/li',
				'<li',
				['a' => ['href' => '#first']], 'First', '/a',
				'/li',
				['li' => ['class' => 'last']],
				['a' => ['href' => '#second']], 'Second', '/a',
				'/li',
				'/ul'
			]
		);

		$result = $this->Html->getCrumbList(null, ['url' => '/home', 'text' => '<img src="/home.png" />', 'escape' => false]);
		$this->assertTags(
			$result,
			[
				'<ul',
				['li' => ['class' => 'first']],
				['a' => ['href' => '/home']], 'img' => ['src' => '/home.png'], '/a',
				'/li',
				'<li',
				['a' => ['href' => '#first']], 'First', '/a',
				'/li',
				['li' => ['class' => 'last']],
				['a' => ['href' => '#second']], 'Second', '/a',
				'/li',
				'/ul'
			]
		);
	}

/**
 * test getCrumbList() in Twitter Bootstrap style.
 *
 * @return void
 */
	public function testCrumbListBootstrapStyle() {
		$this->Html->addCrumb('Home', '/', ['class' => 'home']);
		$this->Html->addCrumb('Library', '/lib');
		$this->Html->addCrumb('Data');
		$result = $this->Html->getCrumbList([
			'class' => 'breadcrumb',
			'separator' => '<span class="divider">-</span>',
			'firstClass' => false,
			'lastClass' => 'active'
		]);
		$this->assertTags(
			$result,
			[
				['ul' => ['class' => 'breadcrumb']],
				'<li',
				['a' => ['class' => 'home', 'href' => '/']], 'Home', '/a',
				['span' => ['class' => 'divider']], '-', '/span',
				'/li',
				'<li',
				['a' => ['href' => '/lib']], 'Library', '/a',
				['span' => ['class' => 'divider']], '-', '/span',
				'/li',
				['li' => ['class' => 'active']], 'Data', '/li',
				'/ul'
			]
		);
	}

/**
 * Test GetCrumbList using style of Zurb Foundation.
 *
 * @return void
 */
	public function testCrumbListZurbStyle() {
		$this->Html->addCrumb('Home', '#');
		$this->Html->addCrumb('Features', '#');
		$this->Html->addCrumb('Gene Splicing', '#');
		$this->Html->addCrumb('Home', '#');
		$result = $this->Html->getCrumbList(
			['class' => 'breadcrumbs', 'firstClass' => false, 'lastClass' => 'current']
		);
		$this->assertTags(
			$result,
			[
				['ul' => ['class' => 'breadcrumbs']],
				'<li',
				['a' => ['href' => '#']], 'Home', '/a',
				'/li',
				'<li',
				['a' => ['href' => '#']], 'Features', '/a',
				'/li',
				'<li',
				['a' => ['href' => '#']], 'Gene Splicing', '/a',
				'/li',
				['li' => ['class' => 'current']],
				['a' => ['href' => '#']], 'Home', '/a',
				'/li',
				'/ul'
			], true
		);
	}

/**
 * testLoadConfig method
 *
 * @return void
 */

	public function testLoadConfig() {
		$path = CAKE . 'Test' . DS . 'test_app' . DS . 'Config' . DS;

		$result = $this->Html->loadConfig('htmlhelper_tags', $path);
		$expected = [
			'tags' => [
				'form' => 'start form',
				'formend' => 'finish form',
				'hiddenblock' => '<div class="hidden">%s</div>'
			]
		];
		$this->assertEquals($expected, $result);
		$tags = $this->Html->getAttribute('_tags');
		$this->assertEquals('start form', $tags['form']);
		$this->assertEquals('finish form', $tags['formend']);
		$this->assertEquals('</select>', $tags['selectend']);

		$result = $this->Html->loadConfig(['htmlhelper_minimized.ini', 'ini'], $path);
		$expected = [
			'minimizedAttributeFormat' => 'format'
		];
		$this->assertEquals($expected, $result);
		$this->assertEquals('format', $this->Html->getAttribute('_minimizedAttributeFormat'));
	}

/**
 * testLoadConfigWrongFile method
 *
 * @return void
 * @expectedException ConfigureException
 */
	public function testLoadConfigWrongFile() {
		$this->Html->loadConfig('wrong_file');
	}

/**
 * testLoadConfigWrongReader method
 *
 * @return void
 * @expectedException ConfigureException
 */
	public function testLoadConfigWrongReader() {
		$path = CAKE . 'Test' . DS . 'test_app' . DS . 'Config' . DS;
		$this->Html->loadConfig(['htmlhelper_tags', 'wrong_reader'], $path);
	}

/**
 * test parsing attributes.
 *
 * @return void
 */
	public function testParseAttributeCompact() {
		$helper = new TestHtmlHelper($this->View);
		$compact = ['compact', 'checked', 'declare', 'readonly', 'disabled',
			'selected', 'defer', 'ismap', 'nohref', 'noshade', 'nowrap', 'multiple', 'noresize'];

		foreach ($compact as $attribute) {
			foreach (['true', true, 1, '1', $attribute] as $value) {
				$attrs = [$attribute => $value];
				$expected = ' ' . $attribute . '="' . $attribute . '"';
				$this->assertEquals($expected, $helper->parseAttributes($attrs), '%s Failed on ' . $value);
			}
		}
		$this->assertEquals(' compact="compact"', $helper->parseAttributes(['compact']));

		$attrs = ['class' => ['foo', 'bar']];
		$expected = ' class="foo bar"';
		$this->assertEquals(' class="foo bar"', $helper->parseAttributes($attrs));

		$helper = new Html5TestHelper($this->View);
		$expected = ' require';
		$this->assertEquals($expected, $helper->parseAttributes(['require']));
		$this->assertEquals($expected, $helper->parseAttributes(['require' => true]));
		$this->assertEquals('', $helper->parseAttributes(['require' => false]));
	}

}
