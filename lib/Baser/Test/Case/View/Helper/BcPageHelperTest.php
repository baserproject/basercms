<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcAppView', 'View');
App::uses('BcBaserHelper', 'View/Helper');
App::uses('BcPageHelper', 'View/Helper');


/**
 * BcPage helper library.
 *
 * @package Baser.Test.Case
 * @property BcPageHelper $BcPage
 */
class BcPageHelperTest extends BaserTestCase {
	
/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'baser.View.Helper.BcPageHelper.PageBcPageHelper',
		'baser.Default.SearchIndex',
		'baser.Default.SiteConfig',
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
		'baser.Default.Permission',
		'baser.Default.ThemeConfig',
		'baser.View.Helper.BcContentsHelper.ContentBcContentsHelper',
		'baser.Default.Site',
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
		$this->Page = ClassRegistry::init('Page');
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
 * テスト用に固定ページのデータを取得する
 * 
 * @return array 固定ページのデータ
 */
	public function getPageData($conditions = array(), $fields = array()) {
		$options = array(
  			'conditions' => $conditions,
  			'fields' => $fields,
  			'recursive' => 0
		);
		$pages = $this->Page->find('all', $options);
		if (empty($pages)) {
			return false;
		} else {
			return $pages[0];
		}
	}

/**
 * ページ機能用URLを取得する
 * 
 * @param array $pageId 固定ページID
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getUrlDataProvider
 */
	public function testGetUrl($pageId, $expected, $message = null) {
		// 固定ページのデータ取得
		$conditions = array('Page.id' => $pageId);
		$fields = array('Content.url');
		$page = $this->getPageData($conditions, $fields);

		$result = $this->BcPage->getUrl($page);
		$this->assertEquals($expected, $result, $message);
	}

	public function getUrlDataProvider() {
		return array(
			array(1, '/index'),
			array(2, '/about'),
			array(3, '/service/index'),
			array(4, '/icons'),
			array(5, '/sitemap'),
			array(6, '/m/index'),
		);
	}

/**
 * 公開状態を取得する
 * 
 * @param boolean $status 公開状態
 * @param mixed $begin 公開開始日時
 * @param mixed $end 公開終了日時
 * @param string $expected 期待値
 * @param string $message テスト失敗時、表示するメッセージ
 * @dataProvider allowPublishDataProvider
 */
	public function testAllowPublish($status, $begin, $end, $expected, $message) {
		$data = array(
			'Page' => array(
				'status' => $status,
				'publish_begin' => $begin,
				'publish_end' => $end,
			)
		);
		$result = $this->BcPage->allowPublish($data);
		$this->assertEquals($expected, $result, $message);
	}

	public function allowPublishDataProvider() {
		return array(
			array(true, 0, 0, true, 'statusの値がそのままかえってきません'),
			array(true, '2200-1-1', 0, false, '公開開始日時の前に公開されています'),
			array(true, 0, '1999-1-1', false, '公開終了日時の後に公開されています'),
			array(true, '2199-1-1', '2200-1-1', false, '公開開始日時の前に公開されています'),
			array(true, '1999-1-1', '2000-1-1', false, '公開開始日時の後に公開されています'),
			array(false, '1999-1-1', 0, false, '非公開になっていません'),
		);
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
	public function testGetNextLink($url, $title, $options, $expected) {
		$this->BcPage->request = $this->_getRequest($url);
		$result = $this->BcPage->getNextLink($title, $options);
		$this->assertEquals($expected, $result);
	}

/**
 * ページカテゴリ間の次の記事へのリンクを出力する
 * 
 * @dataProvider getNextLinkDataProvider
 */
	public function testNextLink($url, $title, $options, $expected) {
		$this->BcPage->request = $this->_getRequest($url);
		ob_start();
		$this->BcPage->nextLink($title, $options);
		$result = ob_get_clean();
		$this->assertEquals($expected, $result);
	}

	public function getNextLinkDataProvider() {
		return array(
			array('/company', '', array('overCategory' => false), false), // PC
			array('/company', '次のページへ', array('overCategory' => false), false), // PC
			array('/about', '', array('overCategory' => true), '<a href="/icons" class="next-link">アイコンの使い方 ≫</a>'), // PC
			array('/about', '次のページへ', array('overCategory' => true), '<a href="/icons" class="next-link">次のページへ</a>'), // PC
			array('/s/about', '', array('overCategory' => false), '<a href="/s/icons" class="next-link">アイコンの使い方 ≫</a>'), // smartphone
			array('/s/about', '次のページへ', array('overCategory' => false), '<a href="/s/icons" class="next-link">次のページへ</a>'), // smartphone
			array('/s/sitemap', '', array('overCategory' => true), '<a href="/s/contact" class="next-link">お問い合わせ ≫</a>'), // smartphone
			array('/s/sitemap', '次のページへ', array('overCategory' => true), '<a href="/s/contact" class="next-link">次のページへ</a>'), // smartphone
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
	public function testGetPrevLink($url, $title, $options, $expected) {
		$this->BcPage->request = $this->_getRequest($url);
		$result = $this->BcPage->getPrevLink($title, $options);
		$this->assertEquals($expected, $result);
	}

/**
 * ページカテゴリ間の前の記事へのリンクを出力する
 *
 * @dataProvider getPrevLinkDataProvider
 */
	public function testPrevLink($url, $title, $options, $expected) {
		$this->BcPage->request = $this->_getRequest($url);
		ob_start();
		$this->BcPage->prevLink($title, $options);
		$result = ob_get_clean();
		$this->assertEquals($expected, $result);
	}

	public function getPrevLinkDataProvider() {
		return array(
			array('/company', '', array('overCategory' => false), false), // PC
			array('/company', '前のページへ', array('overCategory' => false), false), // PC
			array('/about', '', array('overCategory' => true), '<a href="/" class="prev-link">≪ トップページ</a>'), // PC
			array('/about', '前のページへ', array('overCategory' => true), '<a href="/" class="prev-link">前のページへ</a>'), // PC
			array('/s/about', '', array('overCategory' => false), '<a href="/s/" class="prev-link">≪ トップページ</a>'), // smartphone
			array('/s/about', '前のページへ', array('overCategory' => false), '<a href="/s/" class="prev-link">前のページへ</a>'), // smartphone
			array('/s/sitemap', '', array('overCategory' => true), '<a href="/s/icons" class="prev-link">≪ アイコンの使い方</a>'), // smartphone
			array('/s/sitemap', '前のページへ', array('overCategory' => true), '<a href="/s/icons" class="prev-link">前のページへ</a>'), // smartphone
		);
	}

/**
 * 固定ページのコンテンツを出力する
 * 
 * @param string $expected 期待値
 * @param string $message テスト失敗時、表示するメッセージ
 * @dataProvider contentDataProvider
 */
	public function testContent($agent, $expected, $message = null) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$this->BcPage->_View->viewVars['pagePath'] = 'service';
		$this->expectOutputRegex('/' . $expected . '/', $message);
		$this->BcPage->content();
	}

	public function contentDataProvider() {
		return array(
			array('', '<h2 class="fontawesome-circle-arrow-down">Service <span>事業案内<\/span><\/h2>', '固定ページのコンテンツを出力できません'),
			array('smartphone', '<h2 class="contents-head">サービス<\/h2>', 'smartphoneで固定ページのコンテンツを出力できません'),
		);
	}

/**
 * treeList
 */
	public function testTreeList() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}