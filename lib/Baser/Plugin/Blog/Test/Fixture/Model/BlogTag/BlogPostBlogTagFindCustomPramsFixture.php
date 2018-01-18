<?php
/**
 * BlogPost Fixture
 */
class BlogPostBlogTagFindCustomPramsFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = ['model' => 'Blog.BlogPost'];

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
			'name' => '記事１',
			'content' => '',
			'detail' => '',
			'blog_category_id' => '1',
			'user_id' => '1',
			'status' => 1,
			'posts_date' => '2017-07-08 13:10:10',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2017-07-08 13:10:09',
			'modified' => '2017-07-09 19:47:20'
		],
		[
			'id' => '2',
			'blog_content_id' => '1',
			'no' => '2',
			'name' => '記事２',
			'content' => '',
			'detail' => '',
			'blog_category_id' => null,
			'user_id' => '1',
			'status' => 1,
			'posts_date' => '2017-07-08 13:10:10',
			'content_draft' => '',
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => '',
			'created' => '2017-07-08 13:10:09',
			'modified' => '2017-07-09 19:48:18'
		],
		[
			'id' => '3',
			'blog_content_id' => '1',
			'no' => '3',
			'name' => '記事３',
			'content' => '',
			'detail' => '',
			'blog_category_id' => null,
			'user_id' => '1',
			'status' => 0,
			'posts_date' => '2017-07-09 19:47:39',
			'content_draft' => null,
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => null,
			'created' => '2017-07-09 19:47:52',
			'modified' => '2017-07-09 19:47:52'
		],
		[
			'id' => '4',
			'blog_content_id' => '2',
			'no' => '1',
			'name' => '記事４',
			'content' => '',
			'detail' => '',
			'blog_category_id' => null,
			'user_id' => '1',
			'status' => 1,
			'posts_date' => '2017-07-09 19:48:53',
			'content_draft' => null,
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => null,
			'created' => '2017-07-09 19:49:06',
			'modified' => '2017-07-09 19:49:38'
		],
		[
			'id' => '5',
			'blog_content_id' => '3',
			'no' => '1',
			'name' => '記事５',
			'content' => '',
			'detail' => '',
			'blog_category_id' => null,
			'user_id' => '1',
			'status' => 1,
			'posts_date' => '2017-07-09 19:50:20',
			'content_draft' => null,
			'detail_draft' => '',
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'eye_catch' => null,
			'created' => '2017-07-09 19:50:49',
			'modified' => '2017-07-09 19:51:03'
		],
	];

}
