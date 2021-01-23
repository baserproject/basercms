<?php

/**
 * BlogPostFixture
 *
 */
class BlogPostModelFixture extends BaserTestFixture
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
			'posts_date' => '2015-01-27 12:57:59',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2015-01-27 12:56:53',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '2',
			'blog_content_id' => '1',
			'no' => '2',
			'name' => '新商品を販売を開始しました。',
			'content' => '<p>新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br />
<br />
新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。</p>',
			'detail' => '<p>詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
&nbsp;</p>',
			'blog_category_id' => '2',
			'user_id' => '2',
			'status' => '1',
			'posts_date' => '2015-01-27 12:57:59',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2015-01-27 12:56:53',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '3',
			'blog_content_id' => '1',
			'no' => '3',
			'name' => '３記事目',
			'content' => '<p>hogehoge新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br />
<br />
新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。</p>',
			'detail' => '<p>詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
&nbsp;</p>',
			'blog_category_id' => '2',
			'user_id' => '1',
			'status' => '1',
			'posts_date' => '2016-02-10 12:57:59',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2015-01-27 12:56:53',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '4',
			'blog_content_id' => '1',
			'no' => '4',
			'name' => '４記事目',
			'content' => '<p>新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br />
<br />
新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。</p>',
			'detail' => '<p>詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
&nbsp;</p>',
			'blog_category_id' => null,
			'user_id' => '1',
			'status' => '1',
			'posts_date' => '2016-02-10 12:57:59',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2015-01-27 12:56:53',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '5',
			'blog_content_id' => '1',
			'no' => '5',
			'name' => '５記事目',
			'content' => '<p>新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br />
<br />
新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。</p>',
			'detail' => '<p>詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
&nbsp;</p>',
			'blog_category_id' => null,
			'user_id' => '1',
			'status' => '0',
			'posts_date' => '2016-02-10 12:57:59',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2015-01-27 12:56:53',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '6',
			'blog_content_id' => '1',
			'no' => '6',
			'name' => '６記事目',
			'content' => '<p>新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br />
<br />
新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。</p>',
			'detail' => '<p>詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
&nbsp;</p>',
			'blog_category_id' => null,
			'user_id' => '1',
			'status' => '1',
			'posts_date' => '2016-02-10 12:57:59',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => '2009-12-09 12:56:53',
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2015-01-27 12:56:53',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '7',
			'blog_content_id' => '2',
			'no' => '7',
			'name' => '７記事目',
			'content' => '<p>新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br />
<br />
新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。</p>',
			'detail' => '<p>詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
&nbsp;</p>',
			'blog_category_id' => null,
			'user_id' => '1',
			'status' => '1',
			'posts_date' => '2016-02-10 12:57:59',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2015-01-27 12:56:53',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '8',
			'blog_content_id' => '2',
			'no' => '8',
			'name' => '８記事目',
			'content' => '<p>新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br />
<br />
新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。</p>',
			'detail' => '<p>詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
<br />
詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br />
&nbsp;</p>',
			'blog_category_id' => 4,
			'user_id' => '1',
			'status' => '1',
			'posts_date' => '2016-02-10 12:57:59',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2015-01-27 12:56:53',
			'modified' => '2015-01-27 12:57:59'
		],
	];

}
