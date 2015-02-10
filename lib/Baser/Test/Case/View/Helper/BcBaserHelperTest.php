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
		'baser.Menu.Menu',
		'baser.Page.Page',
		'baser.default.Content',
		'baser.default.SiteConfig',
		'baser.User.User',
		'baser.UserGroup.UserGroup',
		'baser.Favorite.Favorite',
		'baser.default.Permission',
		'baser.default.PageCategory',
		'baser.default.ThemeConfig',
	);
	
/**
 * View
 * 
 * @var View
 */
	protected $_View;

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->_View = new BcAppView();
		$this->_View->helpers = array('BcBaser');
		$this->_View->loadHelpers();
		$SiteConfig = ClassRegistry::init('SiteConfig');
		$this->_View->BcBaser->siteConfig = $SiteConfig->findExpanded();
		$this->BcBaser = new BcBaserHelper($this->_View);
		$this->BcBaser = $this->_View->BcBaser;
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
 * ログイン状態にする
 */
	protected function _login() {
		$User = ClassRegistry::init('User');
		$user = $User->find('first', array('conditions' => array('User.id' => 1), 'recursive' => -1));
		unset($user['User']['password']);
		$this->BcBaser->set('user', $user['User']);
	}
	
/**
 * ログイン状態を解除する
 */
	protected function _logout() {
		$this->BcBaser->set('user', '');
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
		$topTitle = '｜baserCMS inc. [デモ]';

		// カテゴリがない場合
		$this->BcBaser->setTitle('会社案内');
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社案内' . $topTitle);
		
		// カテゴリがある場合
		$this->BcBaser->_View->set('crumbs', array(
			array('name' => '会社案内', 'url' => '/company/index'),
			array('name' => '会社データ', 'url' => '/company/data')
		));
		$this->BcBaser->setTitle('会社沿革');
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社沿革｜会社データ｜会社案内' . $topTitle);
		
		// カテゴリは存在するが、カテゴリの表示をオフにした場合
		$this->BcBaser->setTitle('会社沿革', false);
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社沿革' . $topTitle);
		
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
		$topTitle = '｜baserCMS inc. [デモ]';

		$this->BcBaser->_View->set('crumbs', array(
			array('name' => '会社案内', 'url' => '/company/index'),
			array('name' => '会社データ', 'url' => '/company/data')
		));
		$this->BcBaser->setTitle('会社沿革');
		
		// カテゴリをオフにした場合
		$this->BcBaser->setCategoryTitle(false);
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社沿革' . $topTitle);
		
		// カテゴリをオンにした場合
		$this->BcBaser->setCategoryTitle(true);
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社沿革｜会社データ｜会社案内' . $topTitle);
		
		// カテゴリを指定した場合
		$this->BcBaser->setCategoryTitle('店舗案内');
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社沿革｜店舗案内' . $topTitle);
		
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
		$expect = 'baser,CMS,コンテンツマネジメントシステム,開発支援';
		$result = $this->BcBaser->getKeywords();
		$this->assertEqual($result, $expect);
		
		// 設定あり
		$expect = 'baserCMS,国産,オープンソース';
		$this->BcBaser->setKeywords($expect);
		$result = $this->BcBaser->getKeywords();
		$this->assertEqual($result, $expect);
		
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
		$topTitle = 'baserCMS inc. [デモ]';

		// 通常
		$this->BcBaser->_View->set('crumbs', array(
			array('name' => '会社案内', 'url' => '/company/index'),
			array('name' => '会社データ', 'url' => '/company/data')
		));
		$this->BcBaser->setTitle('会社沿革');
		$result = $this->BcBaser->getTitle();
		$this->assertEqual($result, '会社沿革｜会社データ｜会社案内｜' . $topTitle);
		
		// 区切り文字を ≫ に変更
		$result = $this->BcBaser->getTitle('≫');
		$this->assertEqual($result, '会社沿革≫会社データ≫会社案内≫' . $topTitle);
		
		// カテゴリタイトルを除外
		$result = $this->BcBaser->getTitle('｜', false);
		$this->assertEqual($result, '会社沿革｜' . $topTitle);
		
		// カテゴリが対象ページと同じ場合に省略する
		$this->BcBaser->setTitle('会社データ');
		$result = $this->BcBaser->getTitle('｜', true);
		$this->assertEqual($result, '会社データ｜会社案内｜' . $topTitle);
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
		$topTitle = 'baserCMS inc. [デモ]';
		$title = '会社データ';

		$this->BcBaser->setTitle($title);
		ob_start();
		$this->BcBaser->title();
		$result = ob_get_clean();
		$this->assertEqual($result, "<title>". $title . '｜' . $topTitle . "</title>\n");
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
		$this->assertEqual($result, Configure::read('App.fullBaseUrl') . '/about');
		
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
		$this->assertEqual($result, Configure::read('App.fullBaseUrl') . '/basercms/index.php/about');
		
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
		// ※ フロントが存在する場合にはフロントのテンプレートを利用する
		$result = $this->BcBaser->getElement(('global_menu'));
		$this->assertTextContains('<ul class="global-menu clearfix">', $result);
		// 強制的にフロントのテンプレートに切り替えた場合
		$result = $this->BcBaser->getElement('crumbs', array(), array('subDir' => false));
		$this->assertEqual($result, '<strong>ホーム</strong>');
		
	}
	
/**
 * エレメントテンプレートを出力する
 * 
 * BcBaserHelper::getElement() をラッピングしているだけなので、最低限のテストのみ
 */
	public function testElement() {
		ob_start();
		$this->BcBaser->element(('global_menu'));
		$result = ob_get_clean();
		$this->assertTextContains('<ul class="global-menu clearfix">', $result);
	}
	
/**
 * ヘッダーテンプレートを出力する
 */
	public function testHeader() {
		ob_start();
		$this->BcBaser->header();
		$result = ob_get_clean();
		$this->assertTextContains('<div id="Header">', $result);
	}
	
/**
 * フッターテンプレートを出力する
 */
	public function testFooter() {
		ob_start();
		$this->BcBaser->footer();
		$result = ob_get_clean();
		$this->assertTextContains('<div id="Footer">', $result);
	}
	
/**
 * ページネーションを出力する
 */
	public function testPagination() {
		ob_start();
		$this->BcBaser->request->params['paging']['Model'] = array(
			'count'		=> 100,
			'pageCount'	=> 3,
			'page'		=> 2,
			'limit'		=> 10,
			'current'	=> null,
			'prevPage'	=> 1,
			'nextPage'	=> 3,
			'options'	=> array(),
			'paramType'	=> 'named'
		);
		$this->BcBaser->pagination();
		$result = ob_get_clean();
		$this->assertTextContains('<div class="pagination">', $result);
	}
	
/**
 * コンテンツ本体を出力する
 */
	public function testContent() {
		ob_start();
		$this->_View->assign('content', 'コンテンツ本体');
		$this->BcBaser->content();
		$result = ob_get_clean();
		$this->assertEqual($result, 'コンテンツ本体');
	}
	
/**
 * セッションメッセージを出力する
 */
	public function testFlash() {
		// TODO コンソールからのセッションのテストをどうするか？そもそもするか？ ryuring
		if(isConsole()) {
			return;
		}
		$messsage = 'エラーが発生しました。';
		App::uses('SessionComponent', 'Controller/Component');
		App::uses('ComponentCollection', 'Controller/Component');
		$Session = new SessionComponent(new ComponentCollection());
		$Session->setFlash($messsage);
		ob_start();
		$this->BcBaser->flash();
		$result = ob_get_clean();
		$this->assertEqual($result, '<div id="MessageBox"><div id="flashMessage" class="message">' . $messsage. '</div></div>');
	}
	
/**
 * コンテンツ内で設定した CSS や javascript をレイアウトテンプレートに出力する
 */
	public function testScripts() {
		
		$themeConfigTag = '<link rel="stylesheet" type="text/css" href="/files/theme_configs/config.css" />';
		// CSS
		$expected = '<link rel="stylesheet" type="text/css" href="/css/admin/layout.css" />';
		$this->BcBaser->css('admin/layout', array('inline' => false));
		ob_start();
		$this->BcBaser->scripts();
		$result = ob_get_clean();
		$result = str_replace($themeConfigTag, '', $result);
		$this->assertEqual($result, $expected);
		$this->_View->assign('css', '');
		// Javascript
		$expected = '<script type="text/javascript" src="/js/admin/startup.js"></script>';
		$this->BcBaser->js('admin/startup', false);
		ob_start();
		$this->BcBaser->scripts();
		$result = ob_get_clean();
		$result = str_replace($themeConfigTag, '', $result);
		$this->assertEqual($result, $expected);
		$this->_View->assign('script', '');
		// meta
		$expected = '<meta name="description" content="説明文" />';
		App::uses('BcHtmlHelper', 'View/Helper');
		$BcHtml = new BcHtmlHelper($this->_View);
		$result = $BcHtml->meta('description', '説明文', array('inline' => false));
		ob_start();
		$this->BcBaser->scripts();
		$result = ob_get_clean();
		$result = str_replace($themeConfigTag, '', $result);
		$this->assertEqual($result, $expected);
		$this->_View->assign('meta', '');
		// ツールバー
		$expected = '<link rel="stylesheet" type="text/css" href="/css/admin/toolbar.css" />';
		$this->BcBaser->set('user', array('User'));
		ob_start();
		$this->BcBaser->scripts();
		$result = ob_get_clean();
		$result = str_replace($themeConfigTag, '', $result);
		$this->assertEqual($result, $expected);
		
	}
	
/**
 * ツールバーエレメントや CakePHP のデバッグ出力を表示
 */
	public function testFunc() {
		Configure::write('debug', 0);

		// 未ログイン
		ob_start();
		$this->BcBaser->func();
		$result = ob_get_clean();
		$this->assertEqual($result, '');

		// ログイン中
		$expects = '<div id="ToolBar">';
		$this->_login();
		$this->BcBaser->set('currentPrefix', 'admin');
		$this->BcBaser->set('authPrefix', 'admin');
		ob_start();
		$this->BcBaser->func();
		$result = ob_get_clean();
		$this->assertTextContains($expects, $result);
		$this->_logout();

		// デバッグモード２
		$expects = '<table class="cake-sql-log"';
		$debug = Configure::read('debug');
		Configure::write('debug', 2);
		ob_start();
		$this->BcBaser->func();
		$result = ob_get_clean();
		$this->assertTextContains($expects, $result);
	}
	
/**
 * サブメニューを設定する
 * 
 * @param array $elements サブメニューエレメント名を配列で指定
 * @param array $expects サブメニュータイトル
 * @dataProvider setSubMenusDataProvider
 */
	public function testSetSubMenus($elements, $expects) {
		$this->_View->subDir = 'admin';
		$this->BcBaser->setSubMenus($elements);
		ob_start();
		$this->BcBaser->subMenu();
		$result = ob_get_clean();
		foreach($expects as $expect) {
			$this->assertTextContains($expect, $result);
		}
	}
	
/**
 * setSubMenus 用のデータプロバイダ
 *
 * @return array
 */
	public function setSubMenusDataProvider() {
		return array(
			array(array('contents'), array('<th>検索インデックスメニュー</th>')),
			array(array('editor_templates', 'site_configs'), array('<th>エディタテンプレートメニュー</th>', '<th>システム設定共通メニュー</th>')),
			array(array('menus', 'tools'), array('<th>メニュー管理メニュー</th>', '<th>ツールメニュー</th>')),
			array(array('plugins', 'themes'), array('<th>プラグイン管理メニュー</th>', '<th>テーマ管理メニュー</th>')),
			array(array('users'), array('<th>ユーザー管理メニュー</th>')),
			array(array('widget_areas'), array('<th>ウィジェットエリア管理メニュー</th>')),
		);
	}
	
/**
 * XMLヘッダタグを出力する
 */
	public function testXmlHeader() {
		// PC
		$expects = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
		ob_start();
		$this->BcBaser->xmlHeader();
		$result = ob_get_clean();
		$this->assertEqual($result, $expects);
		Configure::write('BcRequest.agent', 'mobile');
		// モバイル
		$expects = '<?xml version="1.0" encoding="Shift-JIS" ?>' . "\n";
		ob_start();
		$this->BcBaser->xmlHeader();
		$result = ob_get_clean();
		$this->assertEqual($result, $expects);
	}
	
/**
 * アイコン（favicon）タグを出力する
 */
	public function testIcon() {
		$expects = '<link href="/favicon.ico" type="image/x-icon" rel="icon" /><link href="/favicon.ico" type="image/x-icon" rel="shortcut icon" />' . "\n";
		ob_start();
		$this->BcBaser->icon();
		$result = ob_get_clean();
		$this->assertEqual($result, $expects);
	}
	
/**
 * ドキュメントタイプを指定するタグを出力する
 * 
 * @param string $docType ドキュメントタイプ
 * @param string $expects ドキュメントタイプを指定するタグ
 * @dataProvider docTypeDataProvider
 */
	public function testDocType($docType, $expects) {
		$expects .= "\n";
		ob_start();
		$this->BcBaser->docType($docType);
		$result = ob_get_clean();
		$this->assertEqual($result, $expects);
	}
	
/**
 * docType 用のデータプロバイダ
 * 
 * @return array
 */
	public function docTypeDataProvider() {
		return array(
			array('xhtml-trans', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'),
			array('html5', '<!DOCTYPE html>')
		);
	}
	
/**
 * CSSの読み込みタグを出力する
 */
	public function testCss() {
		// ノーマル
		ob_start();
		$this->BcBaser->css('admin/import');
		$result = ob_get_clean();
		$expected = '<link rel="stylesheet" type="text/css" href="/css/admin/import.css" />';
		$this->assertEqual($result, $expected);
		// 拡張子あり
		ob_start();
		$this->BcBaser->css('admin/import.css');
		$result = ob_get_clean();
		$expected = '<link rel="stylesheet" type="text/css" href="/css/admin/import.css" />';
		$this->assertEqual($result, $expected);
		// インラインオフ（array）
		$this->BcBaser->css('admin/import.css', array('inline' => false));
		$expected = '<link rel="stylesheet" type="text/css" href="/css/admin/import.css" />';
		$result = $this->_View->Blocks->get('css');
		$this->assertEqual($result, $expected);
		$this->_View->Blocks->end();
		// インラインオフ（boolean）
		$this->BcBaser->css('admin/import.css', false);
		$expected = '<link rel="stylesheet" type="text/css" href="/css/admin/import.css" />';
		$this->_View->assign('css', '');
		$this->assertEqual($result, $expected);
	}
	
/**
 * JSの読み込みタグを出力する
 */
	public function testJs() {
		// ノーマル
		ob_start();
		$this->BcBaser->js('admin/startup');
		$result = ob_get_clean();
		$expected = '<script type="text/javascript" src="/js/admin/startup.js"></script>';
		$this->assertEqual($result, $expected);
		// 拡張子あり
		ob_start();
		$this->BcBaser->js('admin/startup.js');
		$result = ob_get_clean();
		$expected = '<script type="text/javascript" src="/js/admin/startup.js"></script>';
		$this->assertEqual($result, $expected);
		// インラインオフ（boolean）
		$this->BcBaser->js('admin/function', false);
		$expected = '<script type="text/javascript" src="/js/admin/function.js"></script>';
		$result = $this->_View->fetch('script');
		$this->assertEqual($result, $expected);
	}
	
/**
 * 画像読み込みタグを出力する
 */
	public function testImg() {
		$expected = '<img src="/img/baser.power.gif" alt="" />';
		ob_start();
		$this->BcBaser->img('baser.power.gif');
		$result = ob_get_clean();
		$this->assertEqual($result, $expected);
	}
	
/**
 * 画像タグを取得する
 * 
 * @param type $path 画像のパス
 * @param type $options オプション
 * @param type $expected 結果
 * @dataProvider getImgDataProvider
 */
	public function testGetImg($path, $options, $expected) {
		$result = $this->BcBaser->getImg($path, $options);
		$this->assertEqual($result, $expected);
	}

/**
 * getImg 用データプロバイダ
 * 
 * @return array
 */
	public function getImgDataProvider() {
		return array(
			array('baser.power.gif', array('alt' => "baserCMSロゴ"), '<img src="/img/baser.power.gif" alt="baserCMSロゴ" />'),
			array('baser.power.gif', array('title' => "baserCMSロゴ"), '<img src="/img/baser.power.gif" title="baserCMSロゴ" alt="" />')
		);
	}
	
/**
 * アンカータグを出力する
 */
	public function testLink() {
		$expected = '<a href="/about">会社案内</a>';
		ob_start();
		$this->BcBaser->link('会社案内', '/about');
		$result = ob_get_clean();
		$this->assertEqual($result, $expected);
	}
	
/**
 * アンカータグを取得する
 * 
 * @param type $title タイトル
 * @param type $url URL
 * @param type $option オプション
 * @param type $expected 結果
 * @dataProvider getLinkDataProvider
 */
	public function testGetLink($title, $url, $option, $expected) {
		if(!empty($option['prefix'])) {
			$this->_getRequest('/admin');
		}
		if(!empty($option['forceTitle'])) {
			$this->_View->viewVars['user']['user_group_id'] = 2;
		}
		if(!empty($option['ssl'])) {
			Configure::write('BcEnv.sslUrl', 'https://localhost/');
		}
		$result = $this->BcBaser->getLink($title, $url, $option);
		$this->assertEqual($result, $expected);
		Configure::write('BcEnv.sslUrl', '');
	}
	
/**
 * getLink 用の データプロバイダ
 * 
 * @return array
 */
	public function getLinkDataProvider() {
		return array(
			array('', '/', array(), '<a href="/"></a>'),
			array('会社案内', '/about', array(), '<a href="/about">会社案内</a>'),
			array('会社案内 & 会社データ', '/about', array('escape' => true), '<a href="/about">会社案内 &amp; 会社データ</a>'),	// エスケープ
			array('固定ページ管理', array('controller' => 'pages', 'action' => 'index'), array('prefix' => true), '<a href="/admin/pages/index">固定ページ管理</a>'),	// プレフィックス
			array('システム設定', array('admin' => true, 'controller' => 'site_configs', 'action' => 'form'), array('forceTitle' => true), '<span>システム設定</span>'),	// 強制タイトル
			array('会社案内', '/about', array('ssl' => true), '<a href="https://localhost/about">会社案内</a>') // SSL
		);
	}
	
/**
 * SSL通信かどうか判定する
 */
	public function testIsSSL() {
		$_SERVER['HTTPS'] = true;
		$this->BcBaser->request = $this->_getRequest('https://localhost/');
		$this->assertEqual($this->BcBaser->isSSL(), true);
	}
	
/**
 * charset メタタグを出力する
 */
	public function testCharset() {
		// PC
		$expected = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		ob_start();
		$this->BcBaser->charset('UTF-8');
		$result = ob_get_clean();
		$this->assertEqual($result, $expected);
		// モバイル
		$expected = '<meta http-equiv="Content-Type" content="text/html; charset=Shift-JIS" />';
		$this->_setAgentSetting('mobile', true);
		$this->_setAgent('mobile');
		ob_start();
		$this->BcBaser->charset();
		$result = ob_get_clean();
		$this->assertEqual($result, $expected);
	}
	
/**
 * コピーライト用の年を出力する
 */
	public function testCopyYear() {
		// 正常系
		$year = date('Y');
		$expected = '2000 - ' . $year;
		ob_start();
		$this->BcBaser->copyYear(2000);
		$result = ob_get_clean();
		$this->assertEqual($result, $expected);
		// 異常系
		$expected = $year;
		ob_start();
		$this->BcBaser->copyYear('はーい');
		$result = ob_get_clean();
		$this->assertEqual($result, $expected);
	}
	
/**
 * 編集画面へのリンクを出力する
 * 
 * setPageEditLink のテストも兼ねる
 */
	public function testEditLink() {
		// リンクなし
		$expected = '';
		$this->BcBaser->setPageEditLink(1);
		ob_start();
		$this->BcBaser->editLink();
		$result = ob_get_clean();
		$this->assertEqual($result, $expected);
		// リンクあり
		$expected = '<a href="/admin/pages/edit/1" class="tool-menu">編集する</a>';
		$this->_View->viewVars['user'] = array('User' => array('id' => 1));
		$this->_View->viewVars['authPrefix'] = Configure::read('Routing.prefixes.0');
		$this->BcBaser->setPageEditLink(1);
		ob_start();
		$this->BcBaser->editLink();
		$result = ob_get_clean();
		$this->assertEqual($result, $expected);
	}
	
/**
 * 編集画面へのリンクが存在するかチェックする
 */
	public function testExistsEditLink() {
		// 存在しない
		$this->BcBaser->setPageEditLink(1);
		$this->assertEqual($this->BcBaser->existsEditLink(), false);
		// 存在する
		$this->_View->viewVars['user'] = array('User' => array('id' => 1));
		$this->_View->viewVars['authPrefix'] = Configure::read('Routing.prefixes.0');
		$this->BcBaser->setPageEditLink(1);
		$this->assertEqual($this->BcBaser->existsEditLink(), true);
	}
	
/**
 * 公開ページへのリンクを出力する
 */
	public function testPublishLink() {
		// リンクなし
		$expected = '';
		ob_start();
		$this->BcBaser->publishLink();
		$result = ob_get_clean();
		$this->assertEqual($result, $expected);	
		// リンクあり
		$expected = '<a href="/" class="tool-menu">公開ページ</a>';
		$this->_View->viewVars['authPrefix'] = Configure::read('Routing.prefixes.0');
		$this->_View->viewVars['publishLink'] = '/';
		ob_start();
		$this->BcBaser->publishLink();
		$result = ob_get_clean();
		$this->assertEqual($result, $expected);	
	}

/**
 * 公開ページへのリンクが存在するかチェックする
 */
	public function testExistsPublishLink() {
		// 存在しない
		$this->assertEqual($this->BcBaser->existsPublishLink(), false);
		// 存在する
		$this->_View->viewVars['authPrefix'] = Configure::read('Routing.prefixes.0');
		$this->_View->viewVars['publishLink'] = '/';
		$this->assertEqual($this->BcBaser->existsPublishLink(), true);
	}
	
/**
 * アップデート処理が必要かチェックする
 * 
 * @param type $baserVersion baserCMSのバージョン
 * @param type $dbVersion データベースのバージョン
 * @param type $expected 結果
 * @dataProvider checkUpdateDataProvider
 */
	public function testCheckUpdate($baserVersion, $dbVersion, $expected) {
		$this->BcBaser->siteConfig['version'] = $dbVersion;
		$this->_View->viewVars['baserVersion'] = $baserVersion;
		$this->assertEqual($this->BcBaser->checkUpdate(), $expected);
	}
	
/**
 * checkUpdate のデータプロバイダ
 * 
 * @return array
 */
	public function checkUpdateDataProvider() {
		return array(
			array('1.0.0', '1.0.0', false),
			array('1.0.1', '1.0.0', true),
			array('1.0.1-beta', '1.0.0', false),
			array('1.0.1', '1.0.0-beta', false)
		);
	}

/**
 * コンテンツを特定するIDを出力する
 */
	public function testContentsName() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * コンテンツを特定するIDを取得する
 * ・キャメルケースで取得
 * ・URLのコントローラー名までを取得
 * ・ページの場合は、カテゴリ名（カテゴリがない場合は Default）
 * ・トップページは、Home
 *
 * @param string $url URL
 * @param string $expects コンテンツ名
 * @param string $ua リクエストのユーザーエージェント
 * @param array $agents 対応する設定のエージェントのリスト
 * @param array $linkedAgents 連動する設定のエージェントのリスト
 *
 * @dataProvider getContentsNameDataProvider
 * 
 * http://192.168.33.10/test.php?case=View%2FHelper%2FBcBaserHelper&baser=true&filter=testGetContentsName
 */
	public function testGetContentsName($url, $expects, $ua = null, array $agents = array(), array $linkedAgents = array()) {
		//Configure周りの設定を全てOFF状態に
		$this->_unsetAgent();
		$this->_unsetAgentLinks();

		if (!empty($ua) && !empty($agents) && in_array($ua, $agents)) {
			$this->_setAgentSetting($ua, true);
			$this->_setAgent($ua);
		}

		//連携を設定
		foreach ($linkedAgents as $linked) {
			$this->_setAgentLink($linked);
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
			//PC
			array('/', 'Home'),
			array('/news', 'News'),
			array('/contact', 'Contact'),
			array('/company', 'Default'),

			//モバイル　対応OFF 連動OFF

			//スマートフォン 対応OFF　連動OFF

			//モバイル　対応ON 連動OFF
			array('/m/', 'Home', 'mobile', array('mobile')),
			array('/m/news', 'News', 'mobile', array('mobile')),
			array('/m/contact', 'Contact', 'mobile', array('mobile')),
			array('/m/company', 'M', 'mobile', array('mobile')),	// 存在しないページ

			//スマートフォン 対応ON　連動OFF
			array('/s/', 'Home', 'smartphone', array('smartphone')),
			array('/s/news', 'News', 'smartphone', array('smartphone')),
			array('/s/contact', 'Contact', 'smartphone', array('smartphone')),
			array('/s/company', 'S', 'smartphone', array('smartphone')),	// 存在しないページ

			//モバイル　対応ON 連動ON
			array('/m/', 'Home', 'mobile', array('mobile'), array('mobile')),
			array('/m/news', 'News', 'mobile', array('mobile'), array('mobile')),
			array('/m/contact', 'Contact', 'mobile', array('mobile'), array('mobile')),
			array('/m/company', 'Default', 'mobile', array('mobile'), array('mobile')),	// 存在しないページ

			//スマートフォン 対応ON　連動ON
			array('/s/', 'Home', 'smartphone', array('smartphone'), array('smartphone')),
			array('/s/news', 'News', 'smartphone', array('smartphone'), array('smartphone')),
			array('/s/contact', 'Contact', 'smartphone', array('smartphone'), array('smartphone')),
			array('/s/company', 'Default', 'smartphone', array('smartphone'), array('smartphone'))	// 存在しないページ
		);
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
 * パンくずリストの要素を追加する
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
 * ページ機能で作成したページの一覧データを取得する
 * 
 * @param int $pageCategoryId 固定ページカテゴリ
 * @param array $options オプション
 * @param array $expected ページリストデータ
 * @dataProvider getPageListDataProvider
 */
	public function testGetPageList($pageCategoryId, $options, $expected) {
		$this->_setAgentSetting('mobile', true);
		$this->_setAgentSetting('smartphone', true);
		$result = $this->BcBaser->getPageList($pageCategoryId, $options);
		$this->assertEqual($result, $expected);
	}
	
/**
 * getPageList 用のデータプロバイダ
 * @return array
 */
	public function getPageListDataProvider() {
		return array(
			array(null, array(), array(
				array('title' => 'PCトップページ', 'url' => '/'), 
				array('title' => 'サービス', 'url' => '/service'), 
				array('title' => '会社案内', 'url' => '/company'),
				array('title' => '採用情報', 'url' => '/recruit'),
				array('title' => 'モバイルトップページ', 'url' => '/m/'),
				array('title' => 'スマートフォントップページ', 'url' => '/s/'),
				array('title' => 'スマートフォン採用情報', 'url' => '/s/recruit'),
				array('title' => 'モバイルサービス', 'url' => '/m/service')
			)),
			array(1, null, array(
				array('title' => 'モバイルトップページ', 'url' => '/m/'),
				array('title' => 'モバイルサービス', 'url' => '/m/service')
			)),
			array(null, array('order' => 'Page.sort DESC'), array(
				array('title' => 'モバイルサービス', 'url' => '/m/service'),
				array('title' => 'スマートフォン採用情報', 'url' => '/s/recruit'),
				array('title' => 'スマートフォントップページ', 'url' => '/s/'),
				array('title' => 'モバイルトップページ', 'url' => '/m/'),
				array('title' => '採用情報', 'url' => '/recruit'),
				array('title' => '会社案内', 'url' => '/company'),
				array('title' => 'サービス', 'url' => '/service'), 
				array('title' => 'PCトップページ', 'url' => '/')
			))
		);
	}
/**
 * ブラウザにキャッシュさせる為のヘッダーを出力する
 */
	public function testCacheHeader() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * httpから始まるURLを取得する
 */
	public function testGetUri() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * 文字列を検索しマークとしてタグをつける
 */
	public function testMark() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * サイトマップを出力する
 */
	public function testSitemap() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * Flashを表示する
 */
	public function testSwf() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * URLをリンクとして利用可能なURLに変換する
 */
	public function testChangePrefixToAlias() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * 現在のログインユーザーが管理者グループかどうかチェックする
 */
	public function testIsAdminUser() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * 現在のページが固定ページかどうかを判定する
 */
	public function testIsPage() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * 現在のページの純粋なURLを取得する
 */
	public function testGetHere() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * 現在のページがページカテゴリのトップかどうかを判定する
 */
	public function testIsCategoryTop() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * ページをエレメントとして読み込む
 */
	public function testPage() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * ウィジェットエリアを出力する
 */
	public function testWidgetArea() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * 指定したURLが現在のURLかどうか判定する
 *
 * @param string $currentUrl 現在のURL
 * @param string $url 引数として与えられるURL
 * @param bool $expects　メソッドの返り値
 *
 * @dataProvider isCurrentUrlDataProvider
 */
	public function testIsCurrentUrl($currentUrl, $url, $expects) {
		$this->BcBaser->request = $this->_getRequest($currentUrl);
		$this->assertEquals($expects, $this->BcBaser->isCurrentUrl($url));
		// --- サブフォルダ+スマートURLオフ ---
		Configure::write('App.baseUrl', '/basercms/index.php');
		$this->BcBaser->request = $this->_getRequest($currentUrl);
		$this->assertEquals($expects, $this->BcBaser->isCurrentUrl($url));
	}
	
/**
 * isCurrentUrl用のデータプロバイダ
 *
 * @return array
 */
	public function isCurrentUrlDataProvider() {
		return array(
			array('/', '/', true),
			array('/index', '/', true),
			array('/', '/index', true),
			array('/company', '/company', true),
			array('/news', '/news', true),
			array('/news/', '/news', false),
			array('/news/index', '/news', false),
			array('/news', '/news/', false),
			array('/news/', '/news/', true),
			array('/news/index', '/news/', true),
			array('/news', '/news/index', false),
			array('/news/', '/news/index', true),
			array('/news/index', '/news/index', true),
			array('/', '/company', false),
			array('/company', '/', false),
			array('/news', '/', false)
		);
	}
	
/**
 * ユーザー名を整形して表示する
 */
	public function testGetUserName() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * コアテンプレートを読み込む
 */
	public function testIncludeCore() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * ロゴを出力する
 */
	public function testLogo() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * メインイメージを出力する
 */
	public function testMainImage() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * メインイメージの取得でidやclassを指定するオプション
 */
	public function testMainImageIdClass()
	{
		$num = 2;
		$idName = 'testIdName';
		$className = 'testClassName';

		//getMainImageを叩いてULを入手(default)
		ob_start();
		$this->BcBaser->mainImage(array('all' => true, 'num' => $num));
		$tags = ob_get_clean();
		$check = preg_match('|<ul id="MainImage">|', $tags) === 1;
		$this->assertTrue($check);


		//getMainImageを叩いてULを入手(id指定)
		ob_start();
		$this->BcBaser->mainImage(array('all' => true, 'num' => $num, 'id' => $idName));
		$tags = ob_get_clean();
		$check = preg_match('|<ul id="' . $idName . '">|', $tags) === 1;
		$this->assertTrue($check);

		//getMainImageを叩いてULを入手(class指定・id非表示)
		ob_start();
		$this->BcBaser->mainImage(array('all' => true, 'num' => $num, 'id' => false, 'class' => $className));
		$tags = ob_get_clean();
		$check = preg_match('|<ul class="' . $className . '">|', $tags) === 1;
		$this->assertTrue($check);
		//getMainImageを叩いてULを入手(全てなし)
		ob_start();
		$this->BcBaser->mainImage(array('all' => true, 'num' => $num, 'id' => false, 'class' => false));
		$tags = ob_get_clean();
		$check = preg_match('|<ul>|', $tags) === 1;
		$this->assertTrue($check);
	}

/**
 * テーマのURLを取得する
 */
	public function testGetThemeUrl() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * テーマのURLを出力する
 */
	public function testThemeUrl() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * ベースとなるURLを取得する
 */
	public function testGetBaseUrl() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * ベースとなるURLを出力する
 */
	public function testBaseUrl() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * サブメニューを出力する
 */
	public function testSubMenu() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * コンテンツナビを出力する
 */
	public function testContentsNavi() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * パンくずリストを出力する
 */
	public function testCrumbsList() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * グローバルメニューを出力する
 */
	public function testGlobalMenu() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * Google Analytics のトラッキングコードを出力する
 */
	public function testGoogleAnalytics() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * Google Maps を出力する
 */
	public function testGoogleMaps() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * 表示件数設定機能を出力する
 */
	public function testListNum() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
/**
 * サイト内検索フォームを出力
 */
	public function testSiteSearchForm() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
}