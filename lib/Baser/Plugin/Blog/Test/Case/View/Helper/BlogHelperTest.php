<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Test.Case
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('HtmlHelper', 'View.Helper');
App::uses('BcTimeHelper', 'View.Helper');
App::uses('BlogHelper', 'Blog.View/Helper');
App::uses('BlogPost', 'Blog.Model');
App::uses('BlogContent', 'Blog.Model');
App::uses('BlogCategory', 'Blog.Model');

/**
 * Blog helper library.
 *
 * @package Baser.Test.Case
 * @property BlogPost $BlogPost
 * @property BlogContent $BlogContent
 * @property BlogHelper $Blog
 */
class BlogHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'plugin.blog.View/Helper/BlogBaserHelper/BlogCategoryTree',	// テスト内で読み込む
		'baser.Default.User',
		'baser.Default.Page',
		'baser.Default.Plugin',
		'baser.Default.BlogComment',
		'baser.Default.BlogContent',
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.BlogTag',
		'plugin.blog.Model/BlogCategoryModel',
		'plugin.blog.Model/BlogPostModel',
		'plugin.blog.Model/BlogPostsBlogTagModel',
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
		$View = new View();
		$View->request->params['Site'] = array(
			'use_subdomain' => null,
			'name' => null,
			'alias' => null,
		);
		$View->request->params['Content'] = [
			'url' => '/news/',
			'name' => 'news',
			'title' => '新着情報'
		];
		$this->Blog = new BlogHelper($View);

		$this->BlogContent = ClassRegistry::init('Blog.BlogContent');
		$this->BlogContent->expects(array());
		$this->Blog->blogContent = Hash::extract($this->BlogContent->read(null, 1), 'BlogContent');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Blog);
		unset($this->BlogContent);
		Router::reload();
		parent::tearDown();
	}

/**
 * ブログコンテンツデータをセットする
 * 
 * @param int $blogContentId ブログコンテンツID
 * @param bool $viewVars viewVarsを設定
 * @dataProvider setContentDataProvider
 */
	public function testSetContent($blogContentId, $viewVars, $expected) {
		if ($viewVars) {		
			$View = new View();
			$View->viewVars = array('blogContent' => array(
				'BlogContent' => array(
					'id' => 3,
					'name' => 'test',
				)
			));
			$this->Blog = new BlogHelper($View);
		}
		$this->Blog->blogContent = null;
		$this->Blog->setContent($blogContentId);
		$this->assertEquals($this->Blog->blogContent['id'], $expected, 'ブログコンテンツデータを正しくセットできません');
	}

	public function setContentDataProvider() {
		return array(
			array(null, false, null),
			array(2, false, 2),
			array(null, true, 3),
		);
	}

/**
 * ブログIDを取得する
 */
	public function testGetCurrentBlogId() {
		$result = $this->Blog->getCurrentBlogId();
		$expects = '1';
		$this->assertEquals($expects, $result, 'ブログIDを正しく取得できません');
	}

/**
 * ブログアカウント名を取得する
 */
	public function testGetBlogName() {
		$result = $this->Blog->getBlogName();
		$expects = 'news';
		$this->assertEquals($expects, $result, 'ブログアカウント名を正しく取得できません');
	}

/**
 * タイトルを取得する
 */
	public function testGetTitle() {
		$result = $this->Blog->getTitle();
		$expects = '新着情報';
		$this->assertEquals($expects, $result, 'タイトルを正しく取得できません');
		
	}

/**
 * ブログの説明文を取得する
 */
	public function testGetDescription() {
		$result = $this->Blog->getDescription();
		$expects = 'baserCMS inc. [デモ] の最新の情報をお届けします。';
		$this->assertEquals($expects, $result, 'ブログの説明文を正しく取得できません');
	}

/**
 * 記事タイトルを取得する
 */
	public function testGetPostTitle() {
		$post = array('BlogPost' => array(
			'blog_content_id' => 2,
			'name' => 'test-name',
			'no' => 4,
		));

		// $link = true
		$result = $this->Blog->getPostTitle($post);
		$this->assertEquals('<a href="/news/archives/4">test-name</a>', $result, '記事タイトルを正しく取得できません');

		// $link = false
		$result  = $this->Blog->getPostTitle($post, false);
		$this->assertEquals('test-name', $result, '記事タイトルを正しく取得できません');
	}

/**
 * 記事へのリンクを取得する
 */
	public function testGetPostLink() {
		$post = array('BlogPost' => array(
			'blog_content_id' => 2,
			'no' => 3,
		));
		$result = $this->Blog->getPostLink($post, 'test-title');
		$this->assertEquals('<a href="/news/archives/3">test-title</a>', $result, '記事へのリンクを正しく取得できません');
	}

/**
 * ブログ記事のURLを取得する
 */
	public function testGetPostLinkUrl() {
		$post = array('BlogPost' => array(
			'blog_content_id' => 2,
			'no' => 3,
		));
		$result = $this->Blog->getPostLinkUrl($post);
		$this->assertEquals('/news/archives/3', $result, '記事へのリンクを正しく取得できません');
	}

/**
 * 記事の本文を取得する
 *
 * @param array $post ブログ記事データ
 * @param boolean $moreText 詳細データを表示するかどうか
 * @param mixied $moreLink 詳細ページへのリンクを表示するかどうか
 * @param mixed $cut 文字をカットするかどうかを真偽値で指定。カットする場合、文字数を数値で入力
 * @dataProvider getPostContentDataProvider
 */
	public function testGetPostContent($moreText, $moreLink, $cut, $expected) {
		$post = array('BlogPost' => array(
			'content' => 'test-content',
			'detail' => 'test-detail',
			'no' => 3
		));
		$result = $this->Blog->getPostContent($post, $moreText, $moreLink, $cut);
		$this->assertEquals($result, $expected, '記事の本文を正しく取得できません');
	}

	public function getPostContentDataProvider() {
		return array(
			array(true, false, false, '<div class="post-body">test-content</div><div id="post-detail">test-detail</div>'),
			array(false, false, false, '<div class="post-body">test-content</div>'),
			array(false, true, false, '<div class="post-body">test-content</div><p class="more"><a href="/news/archives/3#post-detail">≫ 続きを読む</a></p>'),
			array(false, false, 10, 'test-conte'),
		);
	}

/**
 * 詳細情報を取得する
 */
	public function testGetPostDetail() {
		$BlogPost = ClassRegistry::init('BlogPost');
		$post = $BlogPost->find('first', array('conditions' => array('BlogPost.id' => 1)));

		$result = $this->Blog->getPostDetail($post);
		$expects = $post['BlogPost']['detail'];
		$this->assertEquals($expects, $result);

		//30文字限定
		$options = array('cut' => 30);
		$result = $this->Blog->getPostDetail($post, $options);
		$expects = '詳細が入ります。詳細が入ります。詳細が入ります。詳細が入りま';
		$this->assertEquals($expects, $result);
	}

/**
 * 詳細情報を出力する cut option利用時
 */
	public function testPostDetailCut() {
		$BlogPost = ClassRegistry::init('BlogPost');
		$post = $BlogPost->find('first', array('conditions' => array('BlogPost.id' => 1)));

		$this->expectOutputString('詳細が入ります。詳細が入ります。詳細が入ります。詳細が入りま');

		//30文字限定
		$options = array(
			'cut' => 30
		);
		$this->Blog->postDetail($post, $options);
	}

/**
 * 記事が属するカテゴリ名を取得する
 */
	public function testGetCategory() {
		$post = array('BlogCategory' => array(
			'id' => 1,
			'name' => 'release',
			'title' => 'プレスリリース',
		));
		$result = $this->Blog->getCategory($post);
	}

/**
 * タグを取得する
 * 
 * @dataProvider getTagDataProvider
 */
	public function testGetTag($options, $expects) {
		$post = array(
			'BlogTag' => array(
				array('name' => 'test1'),
				array('name' => 'test2'),
			),
			'BlogContent' => array(
				'name' => 'news'
			)
		);
		$result = $this->Blog->getTag($post, $options);
		$this->assertEquals($expects, $result, 'タグを正しく取得できません');
	}
	
	public function getTagDataProvider() {
		return [
			[['separator' => ' , '], '<a href="/news/archives/tag/test1">test1</a> , <a href="/news/archives/tag/test2">test2</a>'],
			[['tag' => false], [
				['name' => 'test1', 'url' => '/news/archives/tag/test1'],
				['name' => 'test2', 'url' => '/news/archives/tag/test2']
			]]	
		];
	}

/**
 * カテゴリ一覧へのURLを取得する
 *
 * @param int $blogCategoryId ブログカテゴリーID
 * @param int $named $options['named']の値
 * @param string $expected 期待値
 * @dataProvider getCategoryUrlDataProvider
 */
	public function testGetCategoryUrl($blogCategoryId, $named, $expected) {
		$options = array(
			'named' => $named,
		);
		$result = $this->Blog->getCategoryUrl($blogCategoryId, $options);
		$this->assertEquals($result, $expected, 'カテゴリ一覧へのURLを正しく取得できません');
	}

	public function getCategoryUrlDataProvider() {
		return array(
			array(1, array(), '/news/archives/category/release'),
			array(2, array(), '/news/archives/category/release/child'),
			array(3, array(), '/news/archives/category/child-no-parent'),
			array(1, array('test1', 'test2'), '/news/archives/category/release/test1/test2'),
		);
	}

/**
 * 登録日
 */
	public function testGetPostDate() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$post = array('BlogPost' => array(
			'posts_date' => '2015-08-10 18:58:07'
		));
		$this->Blog->getPostDate($post);
	}

/**
 * カテゴリーの一覧をリストタグで取得する
 * 
 * @param int $depth 階層
 * @param boolean $count 件数を表示するかどうか
 * @param array $options オプション
 * @param string $expected 期待値
 * @dataProvider getCategoryListDataProvider
 */
	public function testGetCategoryList($depth, $count, $options, $expected) {
		$BlogCategory = ClassRegistry::init('Blog.BlogCategory');
		$categories = $BlogCategory->getCategoryList(1, array('viewCount' => true, 'depth' => 3));
		$result = $this->Blog->getCategoryList($categories, $depth, $count, $options);
		$this->assertEquals($result, $expected, 'カテゴリーの一覧をリストタグで正しく取得できません');
	}

	public function getCategoryListDataProvider() {
		return array(
			array(3, false, array(), '<ul class="depth-1"><li><a href="/news/archives/category/release">プレスリリース</a><ul class="depth-2"><li><a href="/news/archives/category/release/child">子カテゴリ</a></li></ul></li><li><a href="/news/archives/category/child-no-parent">親子関係なしカテゴリ</a></li></ul>'),
			array(1, false, array(), '<ul class="depth-1"><li><a href="/news/archives/category/release">プレスリリース</a></li><li><a href="/news/archives/category/child-no-parent">親子関係なしカテゴリ</a></li></ul>'),
			array(0, false, array(), ''),
			array(3, true, array(), '<ul class="depth-1"><li><a href="/news/archives/category/release">プレスリリース(1)</a><ul class="depth-2"><li><a href="/news/archives/category/release/child">子カテゴリ(2)</a></li></ul></li><li><a href="/news/archives/category/child-no-parent">親子関係なしカテゴリ(0)</a></li></ul>'),
		);
	}

/**
 * 前の記事へのリンクを出力する
 * 
 * @param int $blogContentId ブログコンテンツID
 * @param int $id 記事ID
 * @param int $posts_date 日付
 * @dataProvider prevLinkDataProvider
 */
	public function testPrevLink($blogContentId, $id, $posts_date, $expected) {
		$this->expectOutputString($expected);
		$post = array('BlogPost' => array(
			'blog_content_id' => $blogContentId,
			'id' => $id,
			'posts_date' => $posts_date
		));
		$this->Blog->prevLink($post);
	}

	public function prevLinkDataProvider() {
		return array(
			array(1, 4, '9000-08-10 18:58:07', '<a href="/news/archives/1" class="prev-link">≪ ホームページをオープンしました</a>'),
			array(1, 3, '1000-08-10 18:58:07', ''),
			array(2, 2, '9000-08-10 18:58:07', '<a href="/news/archives/3" class="prev-link">≪ 新商品を販売を開始しました。</a>'),
			array(2, 1, '1000-08-10 18:58:07', ''),
		);
	}

/**
 * 次の記事へのリンクを出力する
 *
 * @param int $blogContentId ブログコンテンツID
 * @param int $id 記事ID
 * @param int $posts_date 日付
 * @dataProvider nextLinkDataProvider
 */
	public function testNextLink($blogContentId, $id, $posts_date, $expected) {
		$this->expectOutputString($expected);
		$post = array('BlogPost' => array(
			'blog_content_id' => $blogContentId,
			'id' => $id,
			'posts_date' => $posts_date
		));
		$this->Blog->nextLink($post);
	}

	public function nextLinkDataProvider() {
		return array(
			array(1, 1, '9000-08-10 18:58:07', ''),
			array(1, 2, '1000-08-10 18:58:07', '<a href="/news/archives/1" class="next-link">ホームページをオープンしました ≫</a>'),
			array(2, 3, '9000-08-10 18:58:07', ''),
			array(2, 4, '1000-08-10 18:58:07', '<a href="/news/archives/2" class="next-link">新商品を販売を開始しました。 ≫</a>'),
		);
	}

/**
 * ブログテンプレートを取得
 * 
 * @param string $theme テーマ名
 * @param array $expected 期待値
 * @dataProvider getBlogTemplatesDataProvider
 */
	public function testGetBlogTemplates($theme, $expected) {
		$this->Blog->BcBaser->siteConfig['theme'] = $theme;
		$result = $this->Blog->getBlogTemplates();
		$this->assertEquals($result, $expected, 'ブログテンプレートを正しく取得できません');
	}

	public function getBlogTemplatesDataProvider() {
		return array(
			array('nada-icons', array('default' => 'default'))
		);
	}

/**
 * 公開状態を取得する
 */
	public function testAllowPublish() {
		$data = array(
			'status' => true,
			'publish_begin' => '2015-08-10 18:58:07',
			'publish_end' => '9000-08-10 18:58:07'
		);
		$result = $this->Blog->allowPublish($data);
		$this->assertEquals($result, 1, '公開状態を正しく取得できません');

		$data['status'] = 0;
		$result = $this->Blog->allowPublish($data);
		$this->assertEquals($result, 0, '公開状態を正しく取得できません');
	}

/**
 * 記事中の画像を取得する
 * 
 * @param int $num 何枚目の画像か順番を指定
 * @param boolean $link 詳細ページへのリンクをつけるかどうか
 * @param array $expected 期待値
 * @dataProvider getPostImgDataProvider
 */
	public function testGetPostImg($num, $link, $expected) {
		$post = array('BlogPost' => array(
			'blog_content_id' => 1,
			'name' => 'test-name ',
			'content' => '<img src="test1.jpg"><img src="test2.jpg">',
			'detail' => '',
			'no' => '',
		));
		$options = array(
			'num' => $num,
			'link' => $link,
		);
		$result = $this->Blog->getPostImg($post, $options);
		$this->assertEquals($expected, $result, '記事中の画像を正しく取得できません');
	}

	public function getPostImgDataProvider() {
		return array(
			array(1, false, '<img src="/img/test1.jpg" alt="test-name "/>'),
			array(2, false, '<img src="/img/test2.jpg" alt="test-name "/>'),
			array(1, true, '<a href="/news/archives/"><img src="/img/test1.jpg" alt="test-name "/></a>'),
			array(3, false, ''),
		);
	}

/**
 * 記事中のタグで指定したIDの内容を取得する
 */
	public function testGetHtmlById() {
		$post = array('BlogPost' => array(
			'content' => '<p id="test-id1">test-content1</p><div id="test-id2">test-content1</div>',
			'detail' => '<p id="test-id1">test-content2</p>',
		));
		$result = $this->Blog->getHtmlById($post, 'test-id1');
		$expected = 'test-content1';
		$this->assertEquals($expected, $result, '記事中のタグで指定したIDの内容を正しく取得できません');
	}

/**
 * 親カテゴリを取得する
 */
	public function testGetParentCategory() {
		$message = '正しく親カテゴリーを取得できません';
		$post = array('BlogCategory' => array('id' => 1));
		$result = $this->Blog->getParentCategory($post);
		$this->assertEmpty($result, $message);

		$post['BlogCategory']['id'] = 2;
		$result = $this->Blog->getParentCategory($post);
		$this->assertEquals('release', $result['BlogCategory']['name'], $message);
	}


/**
 * 同じタグの関連投稿を取得する
 */
	public function testGetRelatedPosts() {
		$post = array(
			'BlogPost' => array(
				'id' => 1,
				'blog_content_id' => 2,
			),
			'BlogTag' => array(
				array('name' => '新製品')
			)
		);
		$result = $this->Blog->getRelatedPosts($post);
		$this->assertEquals($result[0]['BlogPost']['id'], 3, '同じタグの関連投稿を正しく取得できません
			');
		$this->assertEquals($result[1]['BlogPost']['id'], 2, '同じタグの関連投稿を正しく取得できません
			');

		$post['BlogPost']['id'] = 2;
		$post['BlogPost']['blog_content_id'] = 1;
		$result = $this->Blog->getRelatedPosts($post);
		$this->assertEquals($result[0]['BlogPost']['id'], 1, '同じタグの関連投稿を正しく取得できません
			');

		$post['BlogPost']['id'] = 1;
		$post['BlogPost']['blog_content_id'] = 1;
		$result = $this->Blog->getRelatedPosts($post);
		$this->assertEmpty($result, '関連していない投稿を取得しています');

		$post['BlogPost']['id'] = 2;
		$post['BlogPost']['blog_content_id'] = 3;
		$result = $this->Blog->getRelatedPosts($post);
		$this->assertEmpty($result, '関連していない投稿を取得しています');
	}

/**
 * アイキャッチ画像を取得する
 */
	public function testGetEyeCatch() {
		$post = array('BlogPost' => array(
			'blog_content_id' => 1,
			'eye_catch' => 'test-eye_catch.jpg'
		));
		$result = $this->Blog->getEyeCatch($post);
		$expected = '/\/files\/blog\/1\/blog_posts\/test-eye_catch.jpg/';

		$this->assertRegExp($expected, $result, 'アイキャッチ画像を正しく取得できません');
	}

/**
 * メールフォームプラグインのフォームへのリンクを生成する
 */
	public function testMailFormLink() {
		$this->expectOutputString('<a href="/test-contentsName">test-title</a>');
		$this->Blog->mailFormLink('test-title', 'test-contentsName');
	}

/**
 * カテゴリ取得
 */
	public function testGetCategories() {
		$this->loadFixtures('BlogCategoryTree');
		// １階層、かつ、siteId=0
		$categories = $this->Blog->getCategories();
		$this->assertEquals(1, count($categories));
		// サイトフィルター解除
		$categories = $this->Blog->getCategories(['siteId' => false]);
		$this->assertEquals(2, count($categories));
		// 深さ指定（子）
		$categories = $this->Blog->getCategories(['depth' => 2]);
		$this->assertEquals(1, count($categories[0]['BlogCategory']['children']));
		// 深さ指定（孫）
		$categories = $this->Blog->getCategories(['depth' => 3]);
		$this->assertEquals(1, count($categories[0]['BlogCategory']['children'][0]['BlogCategory']['children']));
		// ブログコンテンツID指定
		$categories = $this->Blog->getCategories(['siteId' => null, 'blogContentId' => 1]);
		$this->assertEquals(1, count($categories));
		// 並べ替え指定
		$categories = $this->Blog->getCategories(['siteId' => null, 'order' => 'name']);
		$this->assertEquals(4, $categories[0]['BlogCategory']['id']);
		// 親指定
		$categories = $this->Blog->getCategories(['parentId' => 2]);
		$this->assertEquals(3, $categories[0]['BlogCategory']['id']);
		// スレッド形式
		$categories = $this->Blog->getCategories(['threaded' => true]);
		$this->assertEquals(3, $categories[0]['children'][0]['children'][0]['BlogCategory']['id']);
		// ID指定
		$categories = $this->Blog->getCategories(['id' => 3]);
		$this->assertEquals('孫カテゴリ', $categories[0]['BlogCategory']['title']);
	}

/**
 * 子カテゴリを持っているかどうか
 *
 * BlogCategory::hasChild() のラッピングの為、テストはスルー 
 */
//	public function testHasChildCategory() {}
	
}
