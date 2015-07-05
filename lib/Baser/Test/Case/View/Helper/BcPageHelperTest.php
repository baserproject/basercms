<?php
/**
 * test for BcPageHelper
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since	       baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcAppView', 'View');
App::uses('BcBaserHelper', 'View/Helper');
App::uses('BcPageHelper', 'View/Helper');
/**
 * BcPage helper library.
 *
 * @package       Baser.Test.Case
 * @property      BcPagerHelper $BcBaser
 */
class BcPageHelperTest extends BaserTestCase {
	
/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'baser.View.Helper.BcBaserHelper.MenuBcBaserHelper',
		'baser.View.Helper.BcBaserHelper.PageBcBaserHelper',
		'baser.Default.Page',
		'baser.Default.PluginContent',
		'baser.Default.Content',
		'baser.Default.SiteConfig',
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
		'baser.Default.Permission',
		'baser.Default.PageCategory',
		'baser.Default.ThemeConfig',
	);

/**
 * View
 * 
 * @var View
 */
	protected $_View;
	
/**
 * __construct
 * 
 * @param string $name
 * @param array $data
 * @param string $dataName
 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
	}

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_View = new BcAppView();
		$this->_View->helpers = array('BcBaser', 'BcPage');
		$this->_View->loadHelpers();
		$this->BcBaser = $this->_View->BcBaser;
		$this->BcPage  = $this->_View->BcPage;
		$this->BcPage->BcBaser = $this->_View->BcBaser;
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		Router::reload();
		parent::tearDown();
	}

/**
 * ページカテゴリ間の次の記事へのリンクを取得する
 * @param string $url
 * @param string $title
 * @param array $options オプション（初期値 : array()）
 *	- `class` : CSSのクラス名（初期値 : 'next-link'）
 *	- `arrow` : 表示文字列（初期値 : ' ≫'）
 *	- `overCategory` : 固定ページのカテゴリをまたいで次の記事のリンクを取得するかどうか（初期値 : false）
 * @param string $expected
 * 
 * @dataProvider getNextLinkDataProvider
 */
	public function testGetNextLink($url, $agent, $title, $options, $expected) {
		$this->BcPage->request = $this->_getRequest($url);
		$this->BcPage->beforeRender(null);
		$this->BcPage->request->params['prefix'] = $this->_setAgent($agent);
		$result = $this->BcPage->getNextLink($title, $options);
		$this->assertEquals($expected, $result);
	}

/**
 * ページカテゴリ間の次の記事へのリンクを出力する
 * 
 * @dataProvider getNextLinkDataProvider
 */
	public function testNextLink($url, $agent, $title, $options, $expected) {
		$this->BcPage->request = $this->_getRequest($url);
		$this->BcPage->beforeRender(null);
		$this->BcPage->request->params['prefix'] = $this->_setAgent($agent);
		ob_start();
		echo $this->BcPage->getNextLink($title, $options);
		$result = ob_get_clean();
		$this->assertEquals($expected, $result);
	}

/**
 * getNextLink/nextLink 共通のデータプロバイダ
 *
 * @return array
 */
	public function getNextLinkDataProvider() {
		return array(
			array('/', null, '', array('overCategory' => false), false), // PC
			array('/', null, '次のページへ', array('overCategory' => false), false), // PC
			array('/company', null, '', array('overCategory' => true), '<a href="/service" class="next-link">事業案内 ≫</a>'), // PC
			array('/service', null, '次のページへ', array('overCategory' => true), '<a href="/recruit" class="next-link">次のページへ</a>'), // PC
			array('/mobile/index', 'mobile', '', array('overCategory' => false), false), // mobile
			array('/mobile/index', 'mobile', '次のページへ', array('overCategory' => false), false), // mobile
			array('/mobile/index', 'mobile', '', array('overCategory' => true), '<a href="/m/about" class="next-link">会社案内 ≫</a>'), // mobile
			array('/mobile/index', 'mobile', '次のページへ', array('overCategory' => true), '<a href="/m/about" class="next-link">次のページへ</a>'), // mobile
			array('/smartphone/index', 'smartphone', '', array('overCategory' => false), false), // smartphone
			array('/smartphone/index', 'smartphone', '次のページへ', array('overCategory' => false), false), // smartphone
			array('/smartphone/about', 'smartphone', '', array('overCategory' => true), '<a href="/s/service" class="next-link">サービス ≫</a>'), // smartphone
			array('/smartphone/about', 'smartphone', '次のページへ', array('overCategory' => true), '<a href="/s/service" class="next-link">次のページへ</a>'), // smartphone
		);
	}

/**
 * ページカテゴリ間の前の記事へのリンクを取得する
 * @param string $url
 * @param string $title
 * @param array $options オプション（初期値 : array()）
 *	- `class` : CSSのクラス名（初期値 : 'next-link'）
 *	- `arrow` : 表示文字列（初期値 : ' ≫'）
 *	- `overCategory` : 固定ページのカテゴリをまたいで次の記事のリンクを取得するかどうか（初期値 : false）
 * @param string $expected
 * 
 * @dataProvider getPrevLinkDataProvider
 */	
	public function testGetPrevLink($url, $agent, $title, $options, $expected) {
		$this->BcPage->request = $this->_getRequest($url);
		$this->BcPage->beforeRender(null);
		$this->BcPage->request->params['prefix'] = $this->_setAgent($agent);
		$result = $this->BcPage->getPrevLink($title, $options);
		$this->assertEquals($expected, $result);
	}

/**
 * ページカテゴリ間の前の記事へのリンクを出力する
 *
 * @dataProvider getPrevLinkDataProvider
 */
	public function testPrevLink($url, $agent, $title, $options, $expected) {
		$this->BcPage->request = $this->_getRequest($url);
		$this->BcPage->beforeRender(null);
		$this->BcPage->request->params['prefix'] = $this->_setAgent($agent);
		ob_start();
		echo $this->BcPage->getPrevLink($title, $options);
		$result = ob_get_clean();
		$this->assertEquals($expected, $result);
	}

/**
 * getPrevLink/prevLink 共通のデータプロバイダ
 *
 * @return array
 */
	public function getPrevLinkDataProvider() {
		return array(
			array('/company', null, '', array('overCategory' => false), false), // PC
			array('/company', null, '前のページへ', array('overCategory' => false), false), // PC
			array('/service', null, '', array('overCategory' => true), '<a href="/company" class="prev-link">≪ 会社案内</a>'), // PC
			array('/service', null, '前のページへ', array('overCategory' => true), '<a href="/company" class="prev-link">前のページへ</a>'), // PC
			array('/mobile/about', 'mobile', '', array('overCategory' => false), false), // mobile
			array('/mobile/about', 'mobile', '前のページへ', array('overCategory' => false), false), // mobile
			array('/mobile/about', 'mobile', '', array('overCategory' => true), '<a href="/m/index" class="prev-link">≪ </a>'), // mobile
			array('/mobile/about', 'mobile', '前のページへ', array('overCategory' => true), '<a href="/m/index" class="prev-link">前のページへ</a>'), // mobile
			array('/smartphone/about', 'smartphone', '', array('overCategory' => false), false), // smartphone
			array('/smartphone/about', 'smartphone', '前のページへ', array('overCategory' => false), false), // smartphone
			array('/smartphone/service', 'smartphone', '', array('overCategory' => true), '<a href="/s/about" class="prev-link">≪ 会社案内</a>'), // smartphone
			array('/smartphone/service', 'smartphone', '前のページへ', array('overCategory' => true), '<a href="/s/about" class="prev-link">前のページへ</a>'), // smartphone
		);
	}

}