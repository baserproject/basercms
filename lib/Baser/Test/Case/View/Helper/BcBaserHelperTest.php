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
		parent::setUp();
		$this->BcBaser = new BcBaserHelper(new BcAppView());
	}
	
/**
 * tearDown
 */
	public function tearDown() {
		unset($this->BcBaser);
		Router::reload();
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
 * meta タグ用のキーワードを取得する
 */
	public function testGetKeywords() {
		
		// 設定なし
		$result = $this->BcBaser->getKeywords();
		$this->assertEmpty($result);
		
		// 設定あり
		$this->BcBaser->setKeywords('baserCMS,国産,オープンソース');
		$result = $this->BcBaser->getKeywords();
		$this->assertEqual($result, 'baserCMS,国産,オープンソース');
		
	}
	
/**
 * meta タグ用のページ説明文を取得する
 */
	public function getDescription() {
		
		// 設定なし
		$result = $this->BcBaser->getDescription();
		$this->assertEmpty($result);
		
		// 設定あり
		$this->BcBaser->setDescription('国産オープンソースのホームページです');
		$result = $this->BcBaser->getDescription();
		$this->assertEqual($result, '国産オープンソースのホームページです');
		
	}
	
/**
 * タイトルタグを取得する
 */
	public function testGetTitle() {
		
		// 通常
		$this->BcBaser->_View->set('crumbs', array(
			array('name' => '会社案内', 'url' => '/company/index'),
			array('name' => '会社データ', 'url' => '/company/data')
		));
		$this->BcBaser->setTitle('会社沿革');
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社沿革｜会社データ｜会社案内');
		
		// 区切り文字を ≫ に変更
		$result = $this->BcBaser->getTitle('≫');
		$this->assertEqual($result, '会社沿革≫会社データ≫会社案内');
		
		// カテゴリタイトルを除外
		$result = $this->BcBaser->getTitle('｜', false);
		$this->assertEqual($result, '会社沿革');
		
		// カテゴリが対象ページと同じ場合に省略する
		$this->BcBaser->setTitle('会社データ');
		$result = $this->BcBaser->getTitle('｜', true);
		$this->assertEqual($result, '会社データ｜会社案内');
				
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
 * コンテンツタイトルを取得する
 */
	public function testGetContensTitlte() {
		// 設定なし
		$result = $this->BcBaser->getContentsTitle();
		$this->assertEmpty($result);
		
		// 設定あり
		$this->BcBaser->setTitle('会社データ');
		$result = $this->BcBaser->getContentsTitle();
		$this->assertEqual($result, '会社データ');
	}

/**
 * コンテンツタイトルを出力する
 */
	public function testContentsTitle() {
		$this->BcBaser->setTitle('会社データ');
		ob_start();
		$this->BcBaser->contentsTitle();
		$result = ob_get_clean();
		$this->assertEqual($result, '会社データ');
	}
	
/**
 * タイトルタグを出力する
 */
	public function testTitle() {
		$this->BcBaser->setTitle('会社データ');
		ob_start();
		$this->BcBaser->title();
		$result = ob_get_clean();
		$this->assertEqual($result, "<title>会社データ</title>\n");
	}
	
/**
 * キーワード用のメタタグを出力する
 */
	public function testMetaKeywords() {
		$this->BcBaser->setKeywords('baserCMS,国産,オープンソース');
		ob_start();
		$this->BcBaser->metaKeywords();
		$result = ob_get_clean();
		$excepted = array(
			'meta' => array(
				'name'		=> 'keywords',
				'content'	=> 'baserCMS,国産,オープンソース'
			)
		);
		$this->assertTags($result, $excepted);
	}
	
/**
 * ページ説明文用のメタタグを出力する
 */
	public function testMetaDescription() {
		$this->BcBaser->setDescription('国産オープンソースのホームページです');
		ob_start();
		$this->BcBaser->metaDescription();
		$result = ob_get_clean();
		$excepted = array(
			'meta' => array(
				'name'		=> 'description',
				'content'	=> '国産オープンソースのホームページです'
			)
		);
		$this->assertTags($result, $excepted);
	}
	
/**
 * RSSフィードのリンクタグを出力する
 */
	public function testRss() {
		ob_start();
		$this->BcBaser->rss('ブログ', 'http://localhost/blog/');
		$result = ob_get_clean();
		$excepted = array(
			'link' => array(
				'href'	=> 'http://localhost/blog/',
				'type'	=> 'application/rss+xml',
				'rel'	=> 'alternate',
				'title'	=> 'ブログ'
			)
		);
		$this->assertTags($result, $excepted);
	}
	
/**
 * 現在のページがトップページかどうかを判定する
 */
	public function testIsHome() {
		// PCページ
		$this->BcBaser->request = $this->_getRequest('/');
		$this->assertEqual($this->BcBaser->isHome() , true);
		$this->BcBaser->request = $this->_getRequest('/index');
		$this->assertEqual($this->BcBaser->isHome() , true);
		$this->BcBaser->request = $this->_getRequest('/news/index');
		$this->assertEqual($this->BcBaser->isHome() , false);
		// モバイルページ
		$this->_setAgent('mobile');
		$this->BcBaser->request = $this->_getRequest('/');
		$this->assertEqual($this->BcBaser->isHome() , false);
		$this->BcBaser->request = $this->_getRequest('/s/');
		$this->assertEqual($this->BcBaser->isHome() , false);
		$this->BcBaser->request = $this->_getRequest('/m/');
		$this->assertEqual($this->BcBaser->isHome() , true);
		$this->BcBaser->request = $this->_getRequest('/m/index');
		$this->assertEqual($this->BcBaser->isHome() , true);
		$this->BcBaser->request = $this->_getRequest('/m/news/index');
		$this->assertEqual($this->BcBaser->isHome() , false);
		// スマートフォンページ
		$this->_setAgent('smartphone');
		$this->BcBaser->request = $this->_getRequest('/');
		$this->assertEqual($this->BcBaser->isHome() , false);
		$this->BcBaser->request = $this->_getRequest('/m/');
		$this->assertEqual($this->BcBaser->isHome() , false);
		$this->BcBaser->request = $this->_getRequest('/s/');
		$this->assertEqual($this->BcBaser->isHome() , true);
		$this->BcBaser->request = $this->_getRequest('/s/index');
		$this->assertEqual($this->BcBaser->isHome() , true);
		$this->BcBaser->request = $this->_getRequest('/s/news/index');
		$this->assertEqual($this->BcBaser->isHome() , false);
		Configure::write('BcRequest.agentAlias', '');
	}
	
/**
 * baserCMSが設置されているパスを出力する
 */
	public function testRoot() {
		// ノーマル
		Configure::write('App.baseUrl', '');
		$this->BcBaser->request = $this->_getRequest('/');
		ob_start();
		$this->BcBaser->root();
		$result = ob_get_clean();
		$this->assertEqual($result, '/');
		// スマートURLオフ
		Configure::write('App.baseUrl', 'index.php');
		$this->BcBaser->request = $this->_getRequest('/');
		ob_start();
		$this->BcBaser->root();
		$result = ob_get_clean();
		$this->assertEqual($result, '/index.php/');
		// サブフォルダ+スマートURLオフ
		Configure::write('App.baseUrl', '/basercms/index.php');
		$this->BcBaser->request = $this->_getRequest('/');
		ob_start();
		$this->BcBaser->root();
		$result = ob_get_clean();
		$this->assertEqual($result, '/basercms/index.php/');
	}
	
/**
 * baserCMSが設置されているパスを取得する
 */
	public function getRoot() {
		// ノーマル
		Configure::write('App.baseUrl', '');
		$this->BcBaser->request = $this->_getRequest('/');
		$result = $this->BcBaser->getRoot();
		$this->assertEqual($result, '/');
		// スマートURLオフ
		Configure::write('App.baseUrl', 'index.php');
		$this->BcBaser->request = $this->_getRequest('/');
		$this->BcBaser->getRoot();
		$this->assertEqual($result, '/index.php/');
		// サブフォルダ+スマートURLオフ
		Configure::write('App.baseUrl', '/basercms/index.php');
		$this->BcBaser->request = $this->_getRequest('/');
		$this->BcBaser->getRoot();
		$this->assertEqual($result, '/basercms/index.php/');
	}
	
/**
 * baserCMSの設置フォルダを考慮したURLを出力する
 * 
 * BcBaserHelper::getUrl() をラッピングしているだけなので、最低限のテストのみ
 */
	public function testUrl() {
		ob_start();
		Configure::write('App.baseUrl', '/basercms/index.php');
		$this->BcBaser->request = $this->_getRequest('/');
		$this->BcBaser->url('/about');
		$result = ob_get_clean();
		$this->assertEqual($result, '/basercms/index.php/about');
	}
	
/**
 * baserCMSの設置フォルダを考慮したURLを取得する
 */
	public function testGetUrl() {
		
		// ノーマル
		$result = $this->BcBaser->getUrl('/about');
		$this->assertEqual($result, '/about');
		
		// 省略した場合
		$result = $this->BcBaser->getUrl();
		$this->assertEqual($result, '/');
		
		// フルURL
		$result = $this->BcBaser->getUrl('/about', true);
		$this->assertEqual($result, 'http://' . $_SERVER["HTTP_HOST"] . '/about');
		
		// 配列URL
		$result = $this->BcBaser->getUrl(array(
			'admin'			=> true,
			'plugin'		=> 'blog',
			'controller'	=> 'blog_posts',
			'action'		=> 'edit',
			1
		));
		$this->assertEqual($result, '/admin/blog/blog_posts/edit/1');

		// セッションIDを付加する場合
		// TODO セッションIDを付加する場合、session.use_trans_sid の値が0である必要が
		// があるが、上記の値はセッションがスタートした後では書込不可の為見送り
		/*Configure::write('BcRequest.agent', 'mobile');
		Configure::write('BcAgent.mobile.sessionId', true);
		ini_set('session.use_trans_sid', 0);*/
		
		// --- サブフォルダ+スマートURLオフ ---
		Configure::write('App.baseUrl', '/basercms/index.php');
		$this->BcBaser->request = $this->_getRequest('/');
		
		// ノーマル
		$result = $this->BcBaser->getUrl('/about');
		$this->assertEqual($result, '/basercms/index.php/about');
		
		// 省略した場合
		$result = $this->BcBaser->getUrl();
		
		$this->assertEqual($result, '/basercms/index.php/');
		
		// フルURL
		$result = $this->BcBaser->getUrl('/about', true);
		$this->assertEqual($result, 'http://' . $_SERVER["HTTP_HOST"] . '/basercms/index.php/about');
		
		// 配列URL
		$result = $this->BcBaser->getUrl(array(
			'admin'			=> true,
			'plugin'		=> 'blog',
			'controller'	=> 'blog_posts',
			'action'		=> 'edit',
			1
		));
		$this->assertEqual($result, '/basercms/index.php/admin/blog/blog_posts/edit/1');
		
	}
	
/**
 * エレメントテンプレートのレンダリング結果を取得する
 */
	public function testGetElement() {
		
		// フロント
		$result = $this->BcBaser->getElement(('global_menu'));
		$this->assertTextContains('<ul class="global-menu clearfix">', $result);
		
		// ### 管理画面
		$View = new BcAppView();
		$View->subDir = 'admin';
		$this->BcBaser = new BcBaserHelper($View);
		// 管理画面用のテンプレートがなくフロントのテンプレートがある場合
		$result = $this->BcBaser->getElement(('global_menu'));
		$this->assertTextContains('<ul class="global-menu clearfix">', $result);
		
		// フロントのテンプレートに切り替えた場合
		$result = $this->BcBaser->getElement(('crumbs'), array(), array('subDir' => false));
		$this->assertEqual($result, '');
		
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

/**
 * 指定されたURLに対応しRouterパース済のCakeRequestのインスタンスを返す
 *
 * @param string $url URL
 * @return CakeRequest
 */
	protected function _getRequest($url) {
		$request = new CakeRequest($url);
		Router::setRequestInfo($request);
		$params = Router::parse($request->url);
		$request->addParams($params);
		return $request;
	}

/**
 * ユーザーエージェント判定に利用される値をConfigureに設定
 * bootstrap.phpで行われている処理の代替
 *
 * @param string $key エージェントを表す文字列キー
 * @return void
 */
	protected function _setAgent($key) {
		$agent = Configure::read("BcAgent.{$key}");
		if(empty($agent)) {
			return;
		}
		Configure::write('BcRequest.agent', $key);
		Configure::write('BcRequest.agentPrefix', $agent['prefix']);
		Configure::write('BcRequest.agentAlias', $agent['alias']);
	}

/**
 * コンテンツを特定するIDを取得する
 * ・キャメルケースで取得
 * ・URLのコントローラー名までを取得
 * ・ページの場合は、カテゴリ名（カテゴリがない場合は Default）
 * ・トップページは、Home
 *
 * @param string $agent エージェント
 * @param string $url URL
 * @param string $expects コンテンツ名
 *
 * @dataProvider getContentsNameDataProvider
 */
	public function testGetContensName($agent = null, $url, $expects) {
		if(!empty($agent)) {
			$this->_setAgent($agent);
		}
		$this->BcBaser->request = $this->_getRequest($url);
		$this->assertEquals($expects, $this->BcBaser->getContentsName());
	}

/**
 * getContentsName用のデータプロバイダ
 *
 * @return array
 */
	public function getContentsNameDataProvider() {
		return array(
			array(null, '/', 'Home'),
			array('mobile', '/m/', 'Home'),
			array('smartphone', '/s/', 'Home'),
			array(null, '/news', 'News'),
			array('mobile', '/m/news', 'News'),
			array('smartphone', '/s/news', 'News'),
			array(null, '/contact', 'Contact'),
			array('mobile', '/m/contact', 'Contact'),
			array('smartphone', '/s/contact', 'Contact'),
			array(null, '/company', 'Default'),
			array('mobile', '/m/company', 'Default'),
			array('smartphone', '/s/company', 'Default')
		);
	}
}