<?php

/**
 * BlogPostFixture
 *
 */
class BlogPostBlogBaserHelperFixture extends BaserTestFixture
{
	/**
	 * Name of the object
	 *
	 * @var string
	 */
	public $name = 'BlogPost';

	/**
	 * BlogPostFixture constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		// PostgreSQLの場合、プライマリーキー以外のインデックスが設定されている場合
		// テスト用のテーブルを作成する際に本体用のインデックスとの重複エラーとなってしまうため
		// 別名のインデックス名として作成しなおす
		include_once BASER_PLUGINS . 'Blog' . DS . 'Config' . DS . 'Schema' . DS . 'blog_posts.php';
		$schema = new BlogPostsSchema();
		$schema->tables['blog_posts']['indexes']['test_blog_content_id_no_index'] = $schema->tables['blog_posts']['indexes']['blog_content_id_no_index'];
		unset($schema->tables['blog_posts']['indexes']['blog_content_id_no_index']);
		$this->fields = $schema->tables['blog_posts'];
	}

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => '1',
			'blog_content_id' => '1',
			'no' => '1',
			'name' => 'name1',
			'content' => 'content1',
			'blog_category_id' => '1',
			'user_id' => '1',
			'status' => '1',
			'posts_date' => '2017-02-01 12:57:59',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2017-02-01 12:57:59',
			'modified' => '2016-01-02 12:57:59'
		],
		[
			'id' => '2',
			'blog_content_id' => '1',
			'no' => '2',
			'name' => 'name2',
			'content' => 'content2',
			'blog_category_id' => '2',
			'user_id' => '1',
			'status' => '1',
			'posts_date' => '2016-02-02 12:57:59',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2016-02-02 12:57:59',
			'modified' => '2016-02-02 12:57:59'
		],
		[
			'id' => '3',
			'blog_content_id' => '1',
			'no' => '3',
			'name' => 'name3',
			'content' => 'content3',
			'blog_category_id' => '3',
			'user_id' => '1',
			'status' => '1',
			'posts_date' => '2016-01-02 12:57:59',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2016-01-02 12:57:59',
			'modified' => '2016-01-02 12:57:59'
		]
	];

}
