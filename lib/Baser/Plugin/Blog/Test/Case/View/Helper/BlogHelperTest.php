<?php
/**
 * test for BlogHelper
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

App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('BlogHelper', 'Blog.View/Helper');
App::uses('BlogPost', 'Blog.Model');
App::uses('BlogContent', 'Blog.Model');

/**
 * Blog helper library.
 *
 * @package       Baser.Test.Case
 * @property      BlogHelper $Blog
 * @property      BlogPost $BlogPost
 * @property      BlogContent $BlogContent
 */
class BlogHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'baser.Default.PluginContent',
		'baser.Default.Plugin',
		'baser.Default.BlogCategory',
		'baser.Default.BlogComment',
		'baser.Default.BlogContent',
		'baser.Default.BlogPost',
		'baser.Default.BlogPostsBlogTag',
		'baser.Default.BlogTag',
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
		$this->Blog = new BlogHelper($View);
		$this->BlogPost = ClassRegistry::init('BlogPost');
		$this->post = $this->BlogPost->find('first', array('conditions' => array('BlogPost.id' => 1)));

		$this->BlogContent = ClassRegistry::init('BlogContent');
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
		unset($this->BlogPost);
		unset($this->BlogContent);
		Router::reload();
		parent::tearDown();
	}


/**
 * 詳細情報を取得する
 *
 * @return void
 */
	public function testGetPostDetail() {
		$result = $this->Blog->getPostDetail($this->post);
		$expects = $this->post['BlogPost']['detail'];
		$this->assertEquals($expects, $result);

		//30文字限定
		$options = array(
			'cut' => 30
		);
		$result = $this->Blog->getPostDetail($this->post, $options);
		$expects = '詳細が入ります。詳細が入ります。詳細が入ります。詳細が入りま';
		$this->assertEquals($expects, $result);
	}

/**
 * 詳細情報を出力する
 *
 * @return void
 */
	public function testPostDetail() {
		$this->expectOutputString($this->post['BlogPost']['detail']);
		$this->Blog->postDetail($this->post);
	}

/**
 * 詳細情報を出力する cut option利用時
 *
 * @return void
 */
	public function testPostDetailCut() {
		$this->expectOutputString('詳細が入ります。詳細が入ります。詳細が入ります。詳細が入りま');

		//30文字限定
		$options = array(
			'cut' => 30
		);
		$this->Blog->postDetail($this->post, $options);
	}

/**
 * ブログIDを取得する
 *
 * @return void
 */
	public function testGetBlogId() {
		$result = $this->Blog->getCurrentBlogId();
		$expects = '1';
		$this->assertEquals($expects, $result);
	}

/**
 * ブログIDを出力する
 *
 * @return void
 */
	public function testCurrentBlogId() {
		$this->expectOutputString('1');
		$this->Blog->currentBlogId();
	}

/**
 * ブログアカウント名を取得する
 *
 * @return void
 */
	public function testGetBlogName() {
		$result = $this->Blog->getBlogName();
		$expects = 'news';
		$this->assertEquals($expects, $result);
	}

/**
 * ブログアカウント名を出力する
 *
 * @return void
 */
	public function testBlogName() {
		$this->expectOutputString('news');
		$this->Blog->blogName();
	}

}