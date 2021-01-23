<?php

/**
 * BlogPostFixture
 */
class BlogPostFixture extends BaserTestFixture
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
			'name' => 'ホームページをオープンしました',
			'content' => '<p>本文が入ります。本文が入ります。本文が入ります。本文が入ります。本文が入ります。本文が入ります。本文が入ります。本文が入ります。本文が入ります。<br />
<br />
本文が入ります。本文が入ります。本文が入ります。本文が入ります。本文が入ります。本文が入ります。本文が入ります。本文が入ります。本文が入ります。<br />
&nbsp;</p>',
			'detail' => '<p>詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。</p>',
			'blog_category_id' => '1',
			'user_id' => '1',
			'status' => '1',
			'posts_date' => '2015-08-10 18:58:07',
			'content_draft' => NULL,
			'detail_draft' => NULL,
			'publish_begin' => NULL,
			'publish_end' => NULL,
			'exclude_search' => 0,
			'eye_catch' => NULL,
			'created' => '2015-08-10 18:57:47',
			'modified' => '2015-08-10 18:58:07',
		],
		[
			'id' => '2',
			'blog_content_id' => '1',
			'no' => '2',
			'name' => '新商品を販売を開始しました。',
			'content' => '<p>新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br />
<br />
新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。</p>',
			'detail' => '<p>詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。</p>',
			'blog_category_id' => '1',
			'user_id' => '1',
			'status' => '1',
			'posts_date' => '2015-08-10 18:58:08',
			'content_draft' => NULL,
			'detail_draft' => NULL,
			'publish_begin' => NULL,
			'publish_end' => NULL,
			'exclude_search' => 0,
			'eye_catch' => NULL,
			'created' => '2015-08-10 18:57:47',
			'modified' => '2015-08-10 18:58:08',
		],
	];

}
