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
	public function getPageData($conditions = null, $fields = null) {
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
 * beforeRender
 * 
 */
	public function testBeforeRender() {
	    $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
		$fields = array('url');
		$page = $this->getPageData($conditions, $fields);

		$result = $this->BcPage->getUrl($page);
		$this->assertEquals($expected, $result, $message);
	}

/**
 * getUrl用データプロバイダ
 *
 * @return array
 */
	public function getUrlDataProvider() {
		return array(
			array(1, '/'),
			array(2, '/company'),
			array(3, '/service'),
			array(4, '/recruit'),
			array(5, '/m/'),
			array(6, '/s/'),
		);
	}

/**
 * 現在のページが所属するカテゴリデータを取得する
 * 
 * MEMO : コンソールから実行すると失敗する
 * 
 * @param array $pageId 固定ページID
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getCategoryDataProvider
 */
	public function testGetCategory($pageId, $expected, $message = null) {
		// 固定ページのデータ取得
		$conditions = array('Page.id' => $pageId);
		$fields = array('PageCategory.id');
		$this->BcPage->request->data = $this->getPageData($conditions, $fields);

		$result = $this->BcPage->getCategory();
		$this->assertEquals($expected, $result, $message);
	}

/**
 * getCategory用データプロバイダ
 *
 * @return array
 */
	public function getCategoryDataProvider() {
		return array(
			array(1, false),
			array(2, false),
			array(3, false),
			array(4, false),
			array(5, array('id' => '1'), 'カテゴリのデータを取得できません'),
			array(6, array('id' => '2'), 'カテゴリのデータを取得できません'),
			array(999, false),
		);
	}

/**
 * 現在のページが所属する親のカテゴリを取得する
 * 
 * @param array $pageId 固定ページID
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getParentCategoryDataProvider
 */
	public function testGetParentCategory($pageId, $expected, $message = null) {
		// 固定ページのデータ取得
		$conditions = array('Page.id' => $pageId);
		$fields = array('PageCategory.id');
		$this->BcPage->request->data = $this->getPageData($conditions, $fields);

		$result = $this->BcPage->getParentCategory();
		$this->assertEquals($expected, $result, $message);		
	}

/**
 *  getParentCategory用データプロバイダ
 *
 * @return array
 */
	public function getParentCategoryDataProvider() {
		return array(
			array(1, false),
			array(2, false),
			array(5, array()),
			array(6, array()),
			array(12, array('PageCategory' => array(
				'id' => '2',
        'parent_id' => null,
        'lft' => '3',
        'rght' => '4',
        'name' => 'smartphone',
        'title' => 'スマートフォン',
        'sort' => '1',
        'contents_navi' => false,
				'owner_id' => null,
				'layout_template' => '',
        'content_template' => '',
        'modified' => null,
        'created' => '2015-01-27 12:56:52'
			)),
			'親カテゴリを取得できません'),
			array(999, false),

		);
	}


/**
 * ページリストを取得する
 * 
 * @param int $pageCategoryId カテゴリID
 * @param int $recursive 関連データの階層	
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getPageListDataProvider
 */
	public function testGetPageList($pageCategoryId, $recursive, $expected, $message = null) {
		$result = $this->BcPage->getPageList($pageCategoryId, $recursive);
		$this->assertEquals($expected, $result, $message);		
	}

/**
 * getPageList用データプロバイダ
 *
 * @return array
 */
	public function getPageListDataProvider() {
		return array(
			array(1, null, array(
				'pages' => array(
					array('Page'=>array('name' => 'index','title' => '','url' => '/m/index')),
					array('Page'=>array('name' => 'about','title' => '会社案内','url' => '/m/about')),
				)),
			'カテゴリからページリストを取得できません'),
			array(2, null, array(
				'pages' => array(
					array('Page'=>array('name' => 'index','title' => '', 'url' => '/s/index')),
					array('Page'=>array('name' => 'about','title' => '会社案内','url' => '/s/about')),
					array('Page'=>array('name' => 'service','title' => 'サービス','url' => '/s/service')),
					array('Page'=>array('name' => 'sitemap','title' => 'サイトマップ','url' => '/s/sitemap')),
					array('Page'=>array('name' => 'icons','title' => 'アイコンの使い方','url' => '/s/icons')),
				),
				'pageCategories' => array(
					array(
						'PageCategory' => array('id' => '3','title' => 'ガラホ'),
						'children' => array('pages' =>array(
								array(
									'Page'=>array('name' => 'garaphone','title' => 'ガラホ','url' => '/gh/')
								)
							)
						),
					)
				)),
			'子カテゴリをもったカテゴリからページリストを取得できません'),
			array(2, 0, array(
				'pages' => array(
					array('Page'=>array('name' => 'index','title' => '', 'url' => '/s/index')),
					array('Page'=>array('name' => 'about','title' => '会社案内','url' => '/s/about')),
					array('Page'=>array('name' => 'service','title' => 'サービス','url' => '/s/service')),
					array('Page'=>array('name' => 'sitemap','title' => 'サイトマップ','url' => '/s/sitemap')),
					array('Page'=>array('name' => 'icons','title' => 'アイコンの使い方','url' => '/s/icons')),
				),
				'pageCategories' => array(
					array(
						'PageCategory' => array('id' => '3','title' => 'ガラホ'),
					)
				)
			),
			'$recursive(関連データの階層)を指定できません'),
			array(2, 2, array(
				'pages' => array(
					array('Page'=>array('name' => 'index','title' => '', 'url' => '/s/index')),
					array('Page'=>array('name' => 'about','title' => '会社案内','url' => '/s/about')),
					array('Page'=>array('name' => 'service','title' => 'サービス','url' => '/s/service')),
					array('Page'=>array('name' => 'sitemap','title' => 'サイトマップ','url' => '/s/sitemap')),
					array('Page'=>array('name' => 'icons','title' => 'アイコンの使い方','url' => '/s/icons')),
				),
				'pageCategories' => array(
					array(
						'PageCategory' => array('id' => '3','title' => 'ガラホ'),
						'children' => array('pages' =>array(
								array(
									'Page'=>array('name' => 'garaphone','title' => 'ガラホ','url' => '/gh/')
								)
							)
						),
					)
				)
			),
			'$recursive(関連データの階層)を指定できません'),
			array(3, null, array('pages' => array(
					array('Page'=>array(
						'name' => 'garaphone',
						'title' => 'ガラホ',
						'url' => '/gh/',
					)),
			)),
			'親カテゴリをもったカテゴリからページリストを取得できません'),
			array(4, null, array(), '存在しないカテゴリに対してfalseが返ってきません'),
		);
	}

/**
 * カテゴリ名を取得する
 * 
 * @param array $pageId 固定ページID
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getCategoryNameDataProvider
 */
	public function testGetCategoryName($pageId, $expected, $message = null) {
		// 固定ページのデータ取得
		$conditions = array('Page.id' => $pageId);
		$fields = array('PageCategory.id','PageCategory.name');
		$this->BcPage->request->data = $this->getPageData($conditions, $fields);

		$result = $this->BcPage->getCategoryName();
		$this->assertEquals($expected, $result, $message);
	}


/**
 * getCategoryName用データプロバイダ
 *
 * @return array
 */
	public function getCategoryNameDataProvider() {
		return array(
			array(0, false, 'IDが0のページは存在しません'),
			array(1, false, 'IDが1のページはカテゴリを持っていません'),
			array(5, 'mobile', 'カテゴリ名を取得できません'),
			array(6, 'smartphone', 'カテゴリ名を取得できません'),
			array(12, 'garaphone', 'カテゴリ名を取得できません'),
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

/**
 * allowPublish用データプロバイダ
 *
 * @return array
 */
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

/**
 * コンテンツナビ有効チェック
 *
 * @param string $expected 期待値
 * @param string $message テスト失敗時、表示するメッセージ
 * @dataProvider contentsNaviAvailableDataProvider
 */
	public function testContentsNaviAvailable($pageId, $expected, $message = null) {
		// 固定ページのデータ取得
		$conditions = array('Page.id' => $pageId);
		$fields = array('Page.page_category_id','PageCategory.id','PageCategory.contents_navi');
		$this->BcPage->request->data = $this->getPageData($conditions, $fields);

		$result = $this->BcPage->contentsNaviAvailable();
		$this->assertEquals($expected, $result, $message);
	}

/**
 * contentsNaviAvailable 共通のデータプロバイダ
 *
 * @return array
 */
	public function contentsNaviAvailableDataProvider() {
		return array(
			array(0, false),
			array(1, false),
			array(5, false),
			array(6, false),
			array(12, true, 'コンテンツナビが有効になっていません'),
		);
	}

/**
 * 固定ページのコンテンツを出力する
 */
	public function testContent() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * テンプレートを取得
 * セレクトボックスのソースとして利用
 */
	public function testGetTemplates() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	public function testTreeList() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}