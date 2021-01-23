<?php

/**
 * BlogCategoryFixture
 *
 */
class BlogCategoryModelFixture extends BaserTestFixture
{
	/**
	 * Name of the object
	 *
	 * @var string
	 */
	public $name = 'BlogCategory';

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
			'name' => 'release',
			'title' => 'プレスリリース',
			'status' => '1',
			'parent_id' => null,
			'lft' => '1',
			'rght' => '4',
			'owner_id' => '1',
			'created' => '2015-01-27 12:56:53',
			'modified' => null
		],
		[
			'id' => '2',
			'blog_content_id' => '1',
			'no' => '2',
			'name' => 'child',
			'title' => '子カテゴリ',
			'status' => '1',
			'parent_id' => '1',
			'lft' => '2',
			'rght' => '3',
			'owner_id' => '1',
			'created' => '2015-01-27 12:56:53',
			'modified' => null
		],
		[
			'id' => '3',
			'blog_content_id' => '1',
			'no' => '3',
			'name' => 'child-no-parent',
			'title' => '親子関係なしカテゴリ',
			'status' => '1',
			'parent_id' => null,
			'lft' => '5',
			'rght' => '6',
			'owner_id' => '1',
			'created' => '2015-01-27 12:56:53',
			'modified' => null
		],
		[
			'id' => '4',
			'blog_content_id' => '2',
			'no' => '1',
			'name' => 'release',
			'title' => 'プレスリリース',
			'status' => '1',
			'parent_id' => null,
			'lft' => '7',
			'rght' => '8',
			'owner_id' => '1',
			'created' => '2015-01-27 12:56:53',
			'modified' => null
		],
	];

}
