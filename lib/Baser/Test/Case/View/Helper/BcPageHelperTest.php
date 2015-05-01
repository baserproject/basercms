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
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Helper);
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
 *  - `prefix` : PC（初期値 : null） or 'mobile' or 'smartphone'
 * @param string $expected
 * 
 * @dataProvider getNextLinkDataProvider
 */
	public function testGetNextLink($url, $title, $options, $expected) {
		//$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$result = '';
		$prefixes = Configure::read('BcAgent');
		//p($prefixes['mobile']['prefix']);exit;

		$options = array_merge(array(
			'class'			=> 'next-link',
			'arrow'			=> ' ≫',
			'overCategory'	=> false,
			'prefix'		=> null
		), $options);

		$arrow = $options['arrow'];
		unset($options['arrow']);
		$overCategory = $options['overCategory'];
		unset($options['overCategory']);
		$prefix = $options['prefix'];
		unset($options['prefix']);

		$this->BcBaser->request = $this->_getRequest($url);
		$PageClass = ClassRegistry::init('Page');
		p($this->BcBaser);exit;
		$pageUrlConditions = $this->getPageUrlConditions($options['prefix']);

		if ($overCategory === true) {
			// ページ情報を持っていない場合はリンクを表示しない
			if (!isset($this->BcBaser->request->data['Page']['sort'])) {
				$result = '';
			}
			$pageUrlConditions = $this->getPageUrlConditions($prefix);
			$conditions = am(array(
				'Page.sort >' => $this->request->data['Page']['sort'],
				'AND' => $pageUrlConditions,
			), $PageClass->getConditionAllowPublish());
		} else {
			$conditions = am(array(
				'Page.sort >' => $this->request->data['Page']['sort'],
				'Page.page_category_id' => $this->request->data['Page']['page_category_id']
			), $PageClass->getConditionAllowPublish());
		}

		$nextPost = $PageClass->find('first', array(
			'conditions' => $conditions,
			'fields' => array('title', 'url'),
			'order' => 'sort',
			'recursive' => -1,
			'cache' => false
		));
		if ($nextPost) {
			if (!$title) {
				$title = $nextPost['Page']['title'] . $arrow;
			}
			$prefixes = Configure::read('BcAgent');
			$result = $this->BcBaser->getLink($title, preg_replace('/^\/' . $prefixes['mobile']['prefix'] . '/', '/' . $prefixes['mobile']['alias'], $nextPost['Page']['url']), $options);
		}

		$this->assertEquals($expected, $result);
	}
	
/**
 * getNextLink 用の データプロバイダ
 *
 * @return array
 */
	public function getNextLinkDataProvider() {
		return array(
			array('/', '次のページへ', array('overCategory' => true, 'prefix' => null), '<a href="/about" class="next-link">次のページへ</a>'), // PC
			//array('次のページへ', array('overCategory' => true, 'prefix' => 'mobile'), ''), // mobile
			//array('次のページへ', array('overCategory' => true, 'prefix' => 'smartphone'), ''), // smartphone
		);
	}

/**
 * ページカテゴリ間の次の記事へのリンクを出力する
 * 
 */
	public function testNextLink() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ページカテゴリ間の前の記事へのリンクを取得する
 *
 */
	public function testGetPrevLink() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ページカテゴリ間の前の記事へのリンクを出力する
 *
 */
	public function testPrevLink() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	protected function getPageUrlConditions($prefix = null) {
		$pageUrlConditions = array();

		if ($prefix) {
			$pageUrlConditions[] = array('Page.url LIKE' => '/' . $prefix . '/%');
		} else {
			// 指定がなければprefixが指定されたページを除外する
			$prefixes = Configure::read('BcAgent');
			foreach($prefixes as $prefix) {
				$pageUrlConditions[] = array('Page.url NOT LIKE' => '/' . $prefix['prefix'] . '/%');
			}
		}
		return $pageUrlConditions;
	}
}