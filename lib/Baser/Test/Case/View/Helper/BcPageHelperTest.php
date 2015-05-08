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
		$result = null;
		
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
		
		$this->BcBaser->request = $this->getRequestData($url);
		$pageUrlConditions = $this->getPageUrlConditions($prefix);
		$PageClass = $this->BcPage->Page;

		if ( (empty($this->BcBaser->request->data['Page']['page_category_id'])
			|| empty($this->BcBaser->request->data['PageCategory']['contents_navi']) ) &&
			$overCategory !== true) {
			$result = false;
		}
		
		if ($overCategory === true) {
			// ページ情報を持っていない場合はリンクを表示しない
			if (!isset($this->BcBaser->request->data['Page']['sort'])) {
				$result = false;
			}
			$pageUrlConditions = $this->getPageUrlConditions($prefix);
			$conditions = am(array(
				'Page.sort >' => $this->BcBaser->request->data['Page']['sort'],
				'AND' => $pageUrlConditions,
			), $PageClass->getConditionAllowPublish());
		} else {
			$conditions = am(array(
				'Page.sort >' => $this->BcBaser->request->data['Page']['sort'],
				'Page.page_category_id' => $this->BcBaser->request->data['Page']['page_category_id']
			), $PageClass->getConditionAllowPublish());
		}

		$nextPost = $PageClass->find('first', array(
			'conditions' => $conditions,
			'fields' => array('title', 'url'),
			'order' => 'sort',
			'recursive' => -1,
			'cache' => false
		));
		
		if ($result === null && $nextPost) {
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
			array('/', '', array('overCategory' => false, 'prefix' => null), false), // PC
			array('/', '次のページへ', array('overCategory' => false, 'prefix' => null), false), // PC
			array('/company', '', array('overCategory' => true, 'prefix' => null), '<a href="/service" class="next-link">事業案内 ≫</a>'), // PC
			array('/service', '次のページへ', array('overCategory' => true, 'prefix' => null), '<a href="/recruit" class="next-link">次のページへ</a>'), // PC
			array('/mobile/index', '', array('overCategory' => false, 'prefix' => 'mobile'), false), // mobile
			array('/mobile/index', '次のページへ', array('overCategory' => false, 'prefix' => 'mobile'), false), // mobile
			array('/mobile/index', '', array('overCategory' => true, 'prefix' => 'mobile'), '<a href="/m/about" class="next-link">会社案内 ≫</a>'), // mobile
			array('/mobile/index', '次のページへ', array('overCategory' => true, 'prefix' => 'mobile'), '<a href="/m/about" class="next-link">次のページへ</a>'), // mobile
			array('/smartphone/index' , '', array('overCategory' => false, 'prefix' => 'smartphone'), false), // smartphone
			array('/smartphone/index' , '次のページへ', array('overCategory' => false, 'prefix' => 'smartphone'), false), // smartphone
			array('/smartphone/about' , '', array('overCategory' => true, 'prefix' => 'smartphone'), '<a href="/smartphone/service" class="next-link">サービス ≫</a>'), // smartphone
			array('/smartphone/about' , '次のページへ', array('overCategory' => true, 'prefix' => 'smartphone'), '<a href="/smartphone/service" class="next-link">次のページへ</a>'), // smartphone
		);
	}

/**
 * ページカテゴリ間の次の記事へのリンクを出力する
 * 
 * @dataProvider nextLinkDataProvider
 */
	public function testNextLink($url, $title, $options, $expected) {
		$result = null;
		
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
		
		$this->BcBaser->request = $this->getRequestData($url);
		$pageUrlConditions = $this->getPageUrlConditions($prefix);
		$PageClass = $this->BcPage->Page;

		if ( (empty($this->BcBaser->request->data['Page']['page_category_id'])
			|| empty($this->BcBaser->request->data['PageCategory']['contents_navi']) ) &&
			$overCategory !== true) {
			$result = false;
		}
		
		if ($overCategory === true) {
			// ページ情報を持っていない場合はリンクを表示しない
			if (!isset($this->BcBaser->request->data['Page']['sort'])) {
				$result = false;
			}
			$pageUrlConditions = $this->getPageUrlConditions($prefix);
			$conditions = am(array(
				'Page.sort >' => $this->BcBaser->request->data['Page']['sort'],
				'AND' => $pageUrlConditions,
			), $PageClass->getConditionAllowPublish());
		} else {
			$conditions = am(array(
				'Page.sort >' => $this->BcBaser->request->data['Page']['sort'],
				'Page.page_category_id' => $this->BcBaser->request->data['Page']['page_category_id']
			), $PageClass->getConditionAllowPublish());
		}

		$nextPost = $PageClass->find('first', array(
			'conditions' => $conditions,
			'fields' => array('title', 'url'),
			'order' => 'sort',
			'recursive' => -1,
			'cache' => false
		));
		
		if ($result === null && $nextPost) {
			if (!$title) {
				$title = $nextPost['Page']['title'] . $arrow;
			}
			$prefixes = Configure::read('BcAgent');
			$result = $this->BcBaser->getLink($title, preg_replace('/^\/' . $prefixes['mobile']['prefix'] . '/', '/' . $prefixes['mobile']['alias'], $nextPost['Page']['url']), $options);
		}

		ob_start();
		echo $result;
		$result = ob_get_clean();
		$this->assertEquals($expected, $result);
	}

/**
 * nextLink 用の データプロバイダ
 *
 * @return array
 */
	public function nextLinkDataProvider() {
		return array(
			array('/', '', array('overCategory' => false, 'prefix' => null), false), // PC
			array('/', '次のページへ', array('overCategory' => false, 'prefix' => null), false), // PC
			array('/company', '', array('overCategory' => true, 'prefix' => null), '<a href="/service" class="next-link">事業案内 ≫</a>'), // PC
			array('/service', '次のページへ', array('overCategory' => true, 'prefix' => null), '<a href="/recruit" class="next-link">次のページへ</a>'), // PC
			array('/mobile/index', '', array('overCategory' => false, 'prefix' => 'mobile'), false), // mobile
			array('/mobile/index', '次のページへ', array('overCategory' => false, 'prefix' => 'mobile'), false), // mobile
			array('/mobile/index', '', array('overCategory' => true, 'prefix' => 'mobile'), '<a href="/m/about" class="next-link">会社案内 ≫</a>'), // mobile
			array('/mobile/index', '次のページへ', array('overCategory' => true, 'prefix' => 'mobile'), '<a href="/m/about" class="next-link">次のページへ</a>'), // mobile
			array('/smartphone/index' , '', array('overCategory' => false, 'prefix' => 'smartphone'), false), // smartphone
			array('/smartphone/index' , '次のページへ', array('overCategory' => false, 'prefix' => 'smartphone'), false), // smartphone
			array('/smartphone/about' , '', array('overCategory' => true, 'prefix' => 'smartphone'), '<a href="/smartphone/service" class="next-link">サービス ≫</a>'), // smartphone
			array('/smartphone/about' , '次のページへ', array('overCategory' => true, 'prefix' => 'smartphone'), '<a href="/smartphone/service" class="next-link">次のページへ</a>'), // smartphone
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
 *  - `prefix` : PC（初期値 : null） or 'mobile' or 'smartphone'
 * @param string $expected
 * 
 * @dataProvider getPrevLinkDataProvider
 */	
	public function testGetPrevLink($url, $title, $options, $expected) {
		$result = null;
		
		$options = array_merge(array(
			'class'			=> 'prev-link',
			'arrow'			=> '≪ ',
			'overCategory'	=> false,
			'prefix'		=> null
		), $options);

		$arrow = $options['arrow'];
		unset($options['arrow']);
		$overCategory = $options['overCategory'];
		unset($options['overCategory']);
		$prefix = $options['prefix'];
		unset($options['prefix']);
		
		$this->BcBaser->request = $this->getRequestData($url);
		$pageUrlConditions = $this->getPageUrlConditions($prefix);
		$PageClass = $this->BcPage->Page;
		
		if ( (empty($this->BcBaser->request->data['Page']['page_category_id'])
			|| empty($this->BcBaser->request->data['PageCategory']['contents_navi']) ) &&
			$overCategory !== true) {
			$result = false;
		}

		if ($overCategory === true) {
			// ページ情報を持っていない場合はリンクを表示しない
			if (!isset($this->BcBaser->request->data['Page']['sort'])) {
				$result = false;
			}
			$pageUrlConditions = $this->getPageUrlConditions($prefix);
			$conditions = am(array(
				'Page.sort <' => $this->BcBaser->request->data['Page']['sort'],
				'AND' => $pageUrlConditions
			), $PageClass->getConditionAllowPublish());
		} else {
			$conditions = am(array(
				'Page.sort <' => $this->BcBaser->request->data['Page']['sort'],
				'Page.page_category_id' => $this->BcBaser->request->data['Page']['page_category_id']
			), $PageClass->getConditionAllowPublish());
		}

		$nextPost = $PageClass->find('first', array(
			'conditions' => $conditions,
			'fields' => array('title', 'url'),
			'order' => 'sort DESC',
			'recursive' => -1,
			'cache' => false
		));
		
		if ($result === null && $nextPost) {
			if (!$title) {
				$title = $arrow . $nextPost['Page']['title'];
			}
			$prefixes = Configure::read('BcAgent');
			$result = $this->BcBaser->getLink($title, preg_replace('/^\/' . $prefixes['mobile']['prefix'] . '/', '/' . $prefixes['mobile']['alias'], $nextPost['Page']['url']), $options);
		}
		$this->assertEquals($expected, $result);
	}

/**
 * getPrevLink 用の データプロバイダ
 *
 * @return array
 */
	public function getPrevLinkDataProvider() {
		return array(
			array('/company', '', array('overCategory' => false, 'prefix' => null), false), // PC
			array('/company', '前のページへ', array('overCategory' => false, 'prefix' => null), false), // PC
			array('/service', '', array('overCategory' => true, 'prefix' => null), '<a href="/company" class="prev-link">≪ 会社案内</a>'), // PC
			array('/service', '前のページへ', array('overCategory' => true, 'prefix' => null), '<a href="/company" class="prev-link">前のページへ</a>'), // PC
			array('/mobile/about', '', array('overCategory' => false, 'prefix' => 'mobile'), false), // mobile
			array('/mobile/about', '前のページへ', array('overCategory' => false, 'prefix' => 'mobile'), false), // mobile
			array('/mobile/about', '', array('overCategory' => true, 'prefix' => 'mobile'), '<a href="/m/index" class="prev-link">≪ </a>'), // mobile
			array('/mobile/about', '前のページへ', array('overCategory' => true, 'prefix' => 'mobile'), '<a href="/m/index" class="prev-link">前のページへ</a>'), // mobile
			array('/smartphone/about' , '', array('overCategory' => false, 'prefix' => 'smartphone'), false), // smartphone
			array('/smartphone/about' , '前のページへ', array('overCategory' => false, 'prefix' => 'smartphone'), false), // smartphone
			array('/smartphone/service' , '', array('overCategory' => true, 'prefix' => 'smartphone'), '<a href="/smartphone/about" class="prev-link">≪ 会社案内</a>'), // smartphone
			array('/smartphone/service' , '前のページへ', array('overCategory' => true, 'prefix' => 'smartphone'), '<a href="/smartphone/about" class="prev-link">前のページへ</a>'), // smartphone
		);
	}

/**
 * ページカテゴリ間の前の記事へのリンクを出力する
 *
 * @dataProvider getPrevLinkDataProvider
 */
	public function testPrevLink($url, $title, $options, $expected) {
		$result = null;
		
		$options = array_merge(array(
			'class'			=> 'prev-link',
			'arrow'			=> '≪ ',
			'overCategory'	=> false,
			'prefix'		=> null
		), $options);

		$arrow = $options['arrow'];
		unset($options['arrow']);
		$overCategory = $options['overCategory'];
		unset($options['overCategory']);
		$prefix = $options['prefix'];
		unset($options['prefix']);
		
		$this->BcBaser->request = $this->getRequestData($url);
		$pageUrlConditions = $this->getPageUrlConditions($prefix);
		$PageClass = $this->BcPage->Page;
		
		if ( (empty($this->BcBaser->request->data['Page']['page_category_id'])
			|| empty($this->BcBaser->request->data['PageCategory']['contents_navi']) ) &&
			$overCategory !== true) {
			$result = false;
		}

		if ($overCategory === true) {
			// ページ情報を持っていない場合はリンクを表示しない
			if (!isset($this->BcBaser->request->data['Page']['sort'])) {
				$result = false;
			}
			$pageUrlConditions = $this->getPageUrlConditions($prefix);
			$conditions = am(array(
				'Page.sort <' => $this->BcBaser->request->data['Page']['sort'],
				'AND' => $pageUrlConditions
			), $PageClass->getConditionAllowPublish());
		} else {
			$conditions = am(array(
				'Page.sort <' => $this->BcBaser->request->data['Page']['sort'],
				'Page.page_category_id' => $this->BcBaser->request->data['Page']['page_category_id']
			), $PageClass->getConditionAllowPublish());
		}

		$nextPost = $PageClass->find('first', array(
			'conditions' => $conditions,
			'fields' => array('title', 'url'),
			'order' => 'sort DESC',
			'recursive' => -1,
			'cache' => false
		));
		
		if ($result === null && $nextPost) {
			if (!$title) {
				$title = $arrow . $nextPost['Page']['title'];
			}
			$prefixes = Configure::read('BcAgent');
			$result = $this->BcBaser->getLink($title, preg_replace('/^\/' . $prefixes['mobile']['prefix'] . '/', '/' . $prefixes['mobile']['alias'], $nextPost['Page']['url']), $options);
		}
		
		ob_start();
		echo $result;
		$result = ob_get_clean();
		
		$this->assertEquals($expected, $result);
	}

/**
 * prevLink 用の データプロバイダ
 *
 * @return array
 */
	public function prevLinkDataProvider() {
		return array(
			array('/company', '', array('overCategory' => false, 'prefix' => null), false), // PC
			array('/company', '前のページへ', array('overCategory' => false, 'prefix' => null), false), // PC
			array('/service', '', array('overCategory' => true, 'prefix' => null), '<a href="/company" class="prev-link">≪ 会社案内</a>'), // PC
			array('/service', '前のページへ', array('overCategory' => true, 'prefix' => null), '<a href="/company" class="prev-link">前のページへ</a>'), // PC
			array('/mobile/about', '', array('overCategory' => false, 'prefix' => 'mobile'), false), // mobile
			array('/mobile/about', '前のページへ', array('overCategory' => false, 'prefix' => 'mobile'), false), // mobile
			array('/mobile/about', '', array('overCategory' => true, 'prefix' => 'mobile'), '<a href="/m/index" class="prev-link">≪ </a>'), // mobile
			array('/mobile/about', '前のページへ', array('overCategory' => true, 'prefix' => 'mobile'), '<a href="/m/index" class="prev-link">前のページへ</a>'), // mobile
			array('/smartphone/about' , '', array('overCategory' => false, 'prefix' => 'smartphone'), false), // smartphone
			array('/smartphone/about' , '前のページへ', array('overCategory' => false, 'prefix' => 'smartphone'), false), // smartphone
			array('/smartphone/service' , '', array('overCategory' => true, 'prefix' => 'smartphone'), '<a href="/smartphone/about" class="prev-link">≪ 会社案内</a>'), // smartphone
			array('/smartphone/service' , '前のページへ', array('overCategory' => true, 'prefix' => 'smartphone'), '<a href="/smartphone/about" class="prev-link">前のページへ</a>'), // smartphone
		);
	}

/**
 * Page.urlのConditionを取得する
 * 
 * @param string $prefix 
 * @return array 
 */
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

/**
 * リクエストデータを取得する
 */
	protected function getRequestData($url) {
		$_SERVER['REQUEST_URI'] = $url;
		Configure::read('BcRequest.pureUrl', getUrlParamFromEnv());
		$this->BcBaser->request = $this->BcPage->request = $this->_getRequest($url);
		$this->BcPage->beforeRender(null);
		return $this->BcBaser->request;
	}
}