<?php
/**
 * test for BlogHelper
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright   Copyright 2008 - 2015, baserCMS Users Community
 * @link      http://basercms.net baserCMS Project
 * @package     Baser.Test.Case.View.Helper
 * @since        baserCMS v 3.0.6
 * @license     http://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('HtmlHelper', 'View.Helper');
App::uses('BcTimeHelper', 'View.Helper');
App::uses('BlogHelper', 'Blog.View/Helper');
App::uses('BlogBaserHelper', 'Blog.View/Helper');
App::uses('BlogPost', 'Blog.Model');
App::uses('BlogContent', 'Blog.Model');
App::uses('BlogCategory', 'Blog.Model');

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
    'baser.Default.User',
    'baser.Default.Page',
    'baser.Default.PluginContent',
    'baser.Default.Plugin',
    'baser.Default.BlogComment',
    'baser.Default.BlogContent',
    'baser.Default.BlogTag',
    'baser.Default.BlogPostsBlogTag',
    'plugin.blog.Model/BlogCategoryModel',
    'plugin.blog.Model/BlogPostModel',
    'plugin.blog.View/Helper/BlogPostBlogBaserHelper',
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
    $this->BlogBaser = new BlogBaserHelper($View);

    $this->BlogContent = ClassRegistry::init('BlogContent');
    $this->BlogContent->expects(array());
    $this->BlogBaser->blogContent = Hash::extract($this->BlogContent->read(null, 1), 'BlogContent');
  }

/**
 * tearDown
 *
 * @return void
 */
  public function tearDown() {
    unset($this->BlogBaser);
    unset($this->BlogContent);
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
    $this->expectOutputRegex($expected, $message);
    $this->BlogBaser->blogPosts($contentsName, $num, $options);
  }

  public function blogPostsProvider() {
    return array(
      array('news', 5, array(), '/name1.*name2.*name3/s', '記事が出力されません'), // 通常
      array('news2', 5, array(), '/記事がありません/'), // statusが0
      array('news3', 5, array(), '/記事がありません/'), // 記事が0
      array('news', 2, array(), '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の件数を正しく指定できません'), // 件数指定
      array('news', 5, array('category' => 'release'), '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事のカテゴリを正しく指定できません'), // カテゴリ指定（子カテゴリあり）
      array('news', 5, array('category' => 'child'), '/^(?!.*name3).*(?!.*name1).*(?=name2).*/s', '記事のカテゴリを正しく指定できません'), // カテゴリ指定(子カテゴリなし)
      array('news', 5, array('tag' => '新製品'), '/^(?!.*name3).*(?!.*name1).*(?=name2).*/s', '記事のタグを正しく指定できません'), // tag指定
      array('news', 5, array('tag' => 'テスト'), '/記事がありません/', '記事のタグを正しく指定できません'), // 存在しないtag指定
      array('news', 5, array('year' => '2016'), '/^(?!.*name1).*(?=name2).*(?=name3).*/s', '記事の年を正しく指定できません'), // 年指定
      array('news', 5, array('year' => '2017'), '/^(?!.*name3).*(?!.*name2).*(?=name1).*/s', '記事の年を正しく指定できません'), // 年指定
      array('news', 5, array('year' => '2999'), '/記事がありません/', '記事の年を正しく指定できません'), // 記事がない年指定
      array('news', 5, array('month' => '2'), '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の月を正しく指定できません'), // 月指定
      array('news', 5, array('day' => '2'), '/^(?!.*name1).*(?=name2).*(?=name3).*/s', '記事の日を正しく指定できません'), // 日指定
      array('news', 5, array('year' => '2016', 'month' => '02', 'day' => '02'), '/^(?!.*name1).*(?!.*name3).*(?=name2).*/s', '記事の年月日を正しく指定できません'), // 年月日指定
      array('news', 5, array('id' => 2), '/^(?!.*name1).*(?!.*name3).*(?=name2).*/s', '記事のIDを正しく指定できません'), // ID指定
      array('news', 5, array('id' => 99), '/記事がありません/', '記事のIDを正しく指定できません'), // 存在しないID指定
      array('news', 5, array('keyword' => '1'), '/^(?!.*name2).*(?!.*name3).*(?=name1).*/s', '記事のキーワードを正しく指定できません'), // キーワード指定
      array('news', 5, array('keyword' => 'content'), '/name1.*name2.*name3/s', '記事のキーワードを正しく指定できません'), // キーワード指定
      array(null, 5, array('contentsTemplate' => 'news'), '/name1.*name2.*name3/s', 'contentsTemplateを正しく指定できません'), // contentsTemplateを指定
      array('news', 5, array('template' => 'archives'), '/プレスリリース/s', 'templateを正しく指定できません'), // template指定
      array('news', 5, array('direction' => 'ASC'), '/name3.*name2.*name1/s', 'templateを正しく指定できません'), // 昇順指定
      array('news', 5, array('direction' => 'DESC'), '/name1.*name2.*name3/s', 'templateを正しく指定できません'), // 降順指定
      array('news', 5, array('sort' => 'modified'), '/name1.*name3.*name2/s', 'sortを正しく指定できません'), // modifiedでソート
      array('news', 2, array('page' => 1), '/^(?!.*name3).*(?=name1).*(?=name2).*/s', 'pageを正しく指定できません'), // ページ指定
      array('news', 2, array('page' => 2), '/^(?!.*name1).*(?!.*name2).*(?=name3).*/s', 'pageを正しく指定できません'), // ページ指定
    );
  }

}