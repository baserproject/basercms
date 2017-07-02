<?php
/**
 * test for BlogHelper
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link      http://basercms.net baserCMS Project
 * @package     Baser.Test.Case.View.Helper
 * @since        baserCMS v 3.0.6
 * @license     http://basercms.net/license/index.html
 */

App::uses('BcBaserHelper', 'View/Helper');

/**
 * Blog helper library.
 *
 * @package       Baser.Test.Case
 * @property      BlogHelper $Blog
 * @property      BlogPost $BlogPost
 * @property      BlogContent $BlogContent
 */
class BlogBaserHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'plugin.blog.View/Helper/BlogBaserHelper/ContentMultiBlog',	// テスト内で読み込む
		'baser.Default.Content', 
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.BlogContent',
		'baser.Default.BlogTag',
		'baser.Default.BlogComment',
		'baser.Default.BlogPostsBlogTag',
		'plugin.blog.Model/BlogCategoryModel',
		'plugin.blog.View/Helper/BlogBaserHelper/BlogPostBlogBaserHelper',
	);
	
/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->BcBaser = new BcBaserHelper(new View());
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->BcBaser);
		Router::reload();
		parent::tearDown();
	}

/**
 * ブログ記事一覧出力
 *
 * @param string | array $contentsName 管理システムで指定したコンテンツ名
 * @param int $num 記事件数
 * @param array $options オプション
 * @param expected string 期待値
 * @param message string テスト失敗時に表示されるメッセージ
 * @dataProvider blogPostsProvider
 */
	public function testBlogPosts($contentsName, $num, $options, $expected, $message = null) {
		$BlogBaser = $this->BcBaser->getPluginBaser('Blog');
		$BlogBaser->request = $this->_getRequest('/');
		$this->expectOutputRegex($expected, $message);
		$this->BcBaser->blogPosts($contentsName, $num, $options);
	}

	public function blogPostsProvider() {
		return array(
//			array('news', 5, array(), '/name1.*name2.*name3/s', '記事が出力されません'), // 通常
//			array('news2', 5, array(), '//'),
//			array('news', 2, array(), '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の件数を正しく指定できません'), // 件数指定
//			array('news', 5, array('category' => 'release'), '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事のカテゴリを正しく指定できません'), // カテゴリ指定（子カテゴリあり）
//			array('news', 5, array('category' => 'child'), '/^(?!.*name3).*(?!.*name1).*(?=name2).*/s', '記事のカテゴリを正しく指定できません'), // カテゴリ指定(子カテゴリなし)
//			array('news', 5, array('tag' => '新製品'), '/^(?!.*name3).*(?!.*name1).*(?=name2).*/s', '記事のタグを正しく指定できません'), // tag指定
//			array('news', 5, array('tag' => 'テスト'), '/記事がありません/', '記事のタグを正しく指定できません'), // 存在しないtag指定
//			array('news', 5, array('year' => '2016'), '/^(?!.*name1).*(?=name2).*(?=name3).*/s', '記事の年を正しく指定できません'), // 年指定
//			array('news', 5, array('year' => '2017'), '/^(?!.*name3).*(?!.*name2).*(?=name1).*/s', '記事の年を正しく指定できません'), // 年指定
//			array('news', 5, array('year' => '2999'), '/記事がありません/', '記事の年を正しく指定できません'), // 記事がない年指定
//			array('news', 5, array('month' => '2'), '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の月を正しく指定できません'), // 月指定
//			array('news', 5, array('day' => '2'), '/^(?!.*name1).*(?=name2).*(?=name3).*/s', '記事の日を正しく指定できません'), // 日指定
//			array('news', 5, array('year' => '2016', 'month' => '02', 'day' => '02'), '/^(?!.*name1).*(?!.*name3).*(?=name2).*/s', '記事の年月日を正しく指定できません'), // 年月日指定
//			array('news', 5, array('id' => 2), '/^(?!.*name1).*(?!.*name3).*(?=name2).*/s', '記事のIDを正しく指定できません'), // ID指定
//			array('news', 5, array('id' => 99), '/記事がありません/', '記事のIDを正しく指定できません'), // 存在しないID指定
//			array('news', 5, array('keyword' => '1'), '/^(?!.*name2).*(?!.*name3).*(?=name1).*/s', '記事のキーワードを正しく指定できません'), // キーワード指定
//			array('news', 5, array('keyword' => 'content'), '/name1.*name2.*name3/s', '記事のキーワードを正しく指定できません'), // キーワード指定
			array(null, 5, array('contentsTemplate' => 'news'), '/name1.*name2.*name3/s', 'contentsTemplateを正しく指定できません'), // contentsTemplateを指定
//			array('news', 5, array('template' => 'archives'), '/プレスリリース/s', 'templateを正しく指定できません'), // template指定
//			array('news', 5, array('direction' => 'ASC'), '/name3.*name2.*name1/s', 'templateを正しく指定できません'), // 昇順指定
//			array('news', 5, array('direction' => 'DESC'), '/name1.*name2.*name3/s', 'templateを正しく指定できません'), // 降順指定
//			array('news', 5, array('sort' => 'modified'), '/name1.*name3.*name2/s', 'sortを正しく指定できません'), // modifiedでソート
//			array('news', 2, array('page' => 1), '/^(?!.*name3).*(?=name1).*(?=name2).*/s', 'pageを正しく指定できません'), // ページ指定
//			array('news', 2, array('page' => 2), '/^(?!.*name1).*(?!.*name2).*(?=name3).*/s', 'pageを正しく指定できません'), // ページ指定
		);
	}
	
/**
 * 全ブログコンテンツの基本情報を取得する
 *
 * @return void
 */
	public function testGetBlogs() {
		// 復数ブログのデータを取得
		$this->loadFixtures('ContentMultiBlog');
		
		// 全件取得
		$blogs = $this->BcBaser->getBlogs();
		$this->assertEquals(3, count($blogs));
		$this->assertEquals(2, $blogs[0]['Content']['id']);

		// ソート順を変更
		$options = array(
			'sort' => 'Content.id DESC',
			'siteId' => 0
		);
		$blogs = $this->BcBaser->getBlogs('', $options);
		$this->assertEquals(3, $blogs[0]['Content']['id']);

		// ブログ指定 1つなので、配列に梱包されてない
		$blogs = $this->BcBaser->getBlogs('news');
		$this->assertEquals('news', $blogs['Content']['name']);
		
		// IDで取得
		$blogs = $this->BcBaser->getBlogs(2);
		$this->assertEquals('topics', $blogs['Content']['name']);
		
		// 復数指定取得
		$blogs = $this->BcBaser->getBlogs(['topics', 'news']);
		$this->assertEquals(2, count($blogs));
	}

/**
 * 現在のページがブログプラグインかどうかを判定する
 *
 * @param bool $expected 期待値
 * @param string $url リクエストURL
 * @return void
 * @dataProvider isBlogDataProvider
 */
	public function testIsBlog($expected, $url) {
		$BlogBaser = $this->BcBaser->getPluginBaser('Blog');
		$BlogBaser->request = $this->_getRequest($url);
		$this->assertEquals($expected, $this->BcBaser->isBlog());
	}

	public function isBlogDataProvider() {
		return array(
			//PC
			array(false, '/'),
			array(false, '/index'),
			array(false, '/contact/index'),
			array(true, '/news/index'),
			// モバイルページ
			array(false, '/m/'),
			array(false, '/m/index'),
			array(false, '/m/contact/index'),
			array(true, '/m/news/index'),
			// スマートフォンページ
			array(false, '/s/'),
			array(false, '/s/index'),
			array(false, '/s/contact/index'),
			array(true, '/s/news/index')
		);
	}

/**
 * ブログのカテゴリ取得
 * 
 * BlogHelper::getCategories() のラッピングの為、呼び出せるかどうかだけテストし、
 * 詳細なテストは、BlogHelper::getCategories() に委ねる
 */
	public function testGetBlogCategories() {
		$categories = $this->BcBaser->getBlogCategories();
		$this->assertEquals(2, count($categories));
	}

/**
 * ブログの子カテゴリを持っているかどうか
 *
 * BlogHelper::hasChildCategory() のラッピングの為、テストはスルー
 */
	//	public function testHasChildBlogCategory() {}
	
}