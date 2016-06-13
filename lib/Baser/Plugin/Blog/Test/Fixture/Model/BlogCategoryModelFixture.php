<?php
/**
 * BlogCategoryFixture
 *
 */
class BlogCategoryModelFixture extends BaserTestFixture {
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
	public $records = array(
		array(
			'id' => '1',
			'blog_content_id' => '1',
			'no' => '1',
			'name' => 'release',
			'title' => 'プレスリリース',
			'status' => '1',
			'parent_id' => null,
			'lft' => '1',
			'rght' => '2',
			'owner_id' => '1',
			'created' => '2015-01-27 12:56:53',
			'modified' => null
		),
		array(
			'id' => '2',
			'blog_content_id' => '1',
			'no' => '2',
			'name' => 'child',
			'title' => '子カテゴリ',
			'status' => '1',
			'parent_id' => '1',
			'lft' => '1',
			'rght' => '2',
			'owner_id' => '1',
			'created' => '2015-01-27 12:56:53',
			'modified' => null
		),
	);

}
