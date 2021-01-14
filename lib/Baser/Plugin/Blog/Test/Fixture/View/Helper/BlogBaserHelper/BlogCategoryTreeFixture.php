<?php

/**
 * BlogCategory Fixture
 */
class BlogCategoryTreeFixture extends CakeTestFixture
{

	/**
	 * Import
	 *
	 * @var array
	 */
	public $import = ['model' => 'Blog.BlogCategory'];

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
			'name' => 'parent',
			'title' => '親',
			'status' => '1',
			'parent_id' => null,
			'lft' => '1',
			'rght' => '6',
			'owner_id' => null,
			'created' => '2017-07-01 18:25:25',
			'modified' => '2017-07-01 22:15:19'
		],
		[
			'id' => '2',
			'blog_content_id' => '1',
			'no' => '2',
			'name' => 'child',
			'title' => '子カテゴリ',
			'status' => null,
			'parent_id' => '1',
			'lft' => '2',
			'rght' => '5',
			'owner_id' => null,
			'created' => '2017-07-01 21:53:01',
			'modified' => '2017-07-01 21:53:01'
		],
		[
			'id' => '3',
			'blog_content_id' => '1',
			'no' => '3',
			'name' => 'grandchild',
			'title' => '孫カテゴリ',
			'status' => null,
			'parent_id' => '2',
			'lft' => '3',
			'rght' => '4',
			'owner_id' => null,
			'created' => '2017-07-01 21:53:35',
			'modified' => '2017-07-01 21:53:35'
		],
		[
			'id' => '4',
			'blog_content_id' => '3',
			'no' => '1',
			'name' => 'another_site',
			'title' => '別サイトカテゴリ',
			'status' => null,
			'parent_id' => null,
			'lft' => '7',
			'rght' => '8',
			'owner_id' => null,
			'created' => '2017-07-01 22:17:03',
			'modified' => '2017-07-01 22:17:03'
		],
	];

}
