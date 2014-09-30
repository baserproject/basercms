<?php
/**
 * test for BcBaserHelper
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since           baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcAppView', 'View');
App::uses('BcBaserHelper', 'View/Helper');

/**
 * BcBaser helper library.
 *
 * @package       Baser.Test.Case
 * @property      BcBaserHelper $BcBaser
 */
class BcBaserHelperTest extends BaserTestCase {
	
/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'baser.Menu',
		'baser.Page'
	);
	
/**
 * setUp
 */
	public function setUp() {
		$this->BcBaser = new BcBaserHelper(new BcAppView());
	}
	
/**
 * tearDown
 */
	public function tearDown() {
		unset($this->BcBaser);
		parent::tearDown();
	}
	
/**
 * コンストラクタ
 */
	public function testConstruct() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * メニューを取得する
 */
	public function testGetMenus() {
		$result = $this->BcBaser->getMenus();
		$this->assertEqual(count($result), 7);
		$this->assertEqual(isset($result[0]['Menu']['id']), true);
	}
	
/**
 * タイトルを設定する
 */
	public function testSetTitle() {
		
		// カテゴリがない場合
		$this->BcBaser->setTitle('会社案内');
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社案内');
		
		// カテゴリがある場合
		$this->BcBaser->_View->set('crumbs', array(
			array('name' => '会社案内', 'url' => '/company/index'),
			array('name' => '会社データ', 'url' => '/company/data')
		));
		$this->BcBaser->setTitle('会社沿革');
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社沿革｜会社データ｜会社案内');
		
		// カテゴリは存在するが、カテゴリの表示をオフにした場合
		$this->BcBaser->setTitle('会社沿革', false);
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社沿革');
		
	}
	
/**
 * meta タグのキーワードを設定する
 */
	public function testSetKeywords() {
		$this->BcBaser->setKeywords('baserCMS,国産,オープンソース');
		$result = $this->BcBaser->getKeywords();
		$this->assertEqual($result, 'baserCMS,国産,オープンソース');
	}
	
/**
 * meta タグの説明文を設定する
 */
	public function testSetDescription() {
		$this->BcBaser->setDescription('国産オープンソースのホームページです');
		$result = $this->BcBaser->getDescription();
		$this->assertEqual($result, '国産オープンソースのホームページです');
	}
	
/**
 * レイアウトで利用する為の変数を設定する
 */
	public function testSet() {
		$this->BcBaser->set('keywords', 'baserCMS,国産,オープンソース');
		$result = $this->BcBaser->getKeywords();
		$this->assertEqual($result, 'baserCMS,国産,オープンソース');
	}
	
/**
 * タイトルへのカテゴリタイトルの出力有無を設定する
 */
	public function testSetCategoryTitle() {
		
		$this->BcBaser->_View->set('crumbs', array(
			array('name' => '会社案内', 'url' => '/company/index'),
			array('name' => '会社データ', 'url' => '/company/data')
		));
		$this->BcBaser->setTitle('会社沿革');
		
		// カテゴリをオフにした場合
		$this->BcBaser->setCategoryTitle(false);
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社沿革');
		
		// カテゴリをオンにした場合
		$this->BcBaser->setCategoryTitle(true);
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社沿革｜会社データ｜会社案内');
		
		// カテゴリを指定した場合
		$this->BcBaser->setCategoryTitle('店舗案内');
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社沿革｜店舗案内');
		
		// パンくず用にリンクも指定した場合
		$this->BcBaser->setCategoryTitle(array(
			'name' => '店舗案内', 
			'url' => '/shop/index'
		));
		$result = $this->BcBaser->getCrumbs();
		$this->assertEqual($result, array(
			array(
				'name'	=> '店舗案内',
				'url'	=> '/shop/index'
			),
			array(
				'name'	=> '会社沿革',
				'url'	=> ''
			)
		));

	}
	
/**
 * パンくずリストの配列を取得する
 */
	public function testGetCrumbs() {
		
		// パンくずが設定されてない場合
		$result = $this->BcBaser->getCrumbs(true);
		$this->assertEmpty($result);
		
		// パンくずが設定されている場合
		$this->BcBaser->_View->set('crumbs', array(
			array('name' => '会社案内', 'url' => '/company/index'),
			array('name' => '会社データ', 'url' => '/company/data')
		));
		$this->BcBaser->setTitle('会社沿革');
		$result = $this->BcBaser->getCrumbs(true);
		$expected = array(
			array('name' => '会社案内', 'url' => '/company/index'),
			array('name' => '会社データ', 'url' => '/company/data'),
			array('name' => '会社沿革', 'url' => '')
		);
		$this->assertEqual($result, $expected);
		
		// パンくずは設定されているが、オプションでカテゴリをオフにした場合
		$result = $this->BcBaser->getCrumbs(false);
		$expected = array(
			array('name' => '会社沿革', 'url' => '')
		);
		$this->assertEqual($result, $expected);
		
	}
	
/**
 * パンくずリストのHTMLレンダリング結果を表示する
 */
	public function testCrumbs() {
		
		// パンくずが設定されてない場合
		$result = $this->BcBaser->crumbs();
		$this->assertEmpty($result);
		
		// パンくずが設定されている場合
		$crumbs = array(
			array('name' => '会社案内', 'url' => '/company/index'),
			array('name' => '会社データ', 'url' => '/company/data'),
			array('name' => '会社沿革', 'url' => '')
		);
		foreach($crumbs as $crumb) {
			$this->BcBaser->addCrumb($crumb['name'], $crumb['url']);
		}
		ob_start();
		$this->BcBaser->crumbs();
		$result = ob_get_clean();
		$expected = array(
			array('a' => array('href' => '/company/index')),
			'会社案内',
			'/a',
			'&raquo;',
			array('a' => array('href' => '/company/data')),
			'会社データ',
			'/a',
			'&raquo;会社沿革'		
		);
		$this->assertTags($result, $expected);
		
		// 区切り文字を変更、先頭にホームを追加
		ob_start();
		$this->BcBaser->crumbs(' | ', 'ホーム');
		$result = ob_get_clean();
		$expected = array(
			array('a' => array('href' => '/')),
			'ホーム',
			'/a',
			' | ',
			array('a' => array('href' => '/company/index')),
			'会社案内',
			'/a',
			' | ',
			array('a' => array('href' => '/company/data')),
			'会社データ',
			'/a',
			' | 会社沿革'	
		);
		$this->assertTags($result, $expected);
		
	}

/**
 * パンくずを追加する
 */
	public function testAddCrumbs() {
		
		$this->BcBaser->addCrumb('会社案内', '/company/index');
		ob_start();
		$this->BcBaser->crumbs();
		$result = ob_get_clean();
		$expected = array(
			array('a' => array('href' => '/company/index')),
			'会社案内',
			'/a'
		);
		$this->assertTags($result, $expected);
		
	}
	
}