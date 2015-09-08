<?php
/**
 * PageCategoryFixture
 *
 */
class PageCategoryBcPageHelperFixture extends BaserTestFixture {

/**
 * Name of the object
 *
 * @var string
 */
	public $name = 'PageCategory';
	
/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'parent_id' => null,
			'lft' => '1',
			'rght' => '2',
			'name' => 'mobile',
			'title' => 'モバイル',
			'sort' => '1',
			'contents_navi' => 0,
			'owner_id' => null,
			'layout_template' => '',
			'content_template' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		),
		array(
			'id' => '2',
			'parent_id' => null,
			'lft' => '3',
			'rght' => '4',
			'name' => 'smartphone',
			'title' => 'スマートフォン',
			'sort' => '1',
			'contents_navi' => 0,
			'owner_id' => null,
			'layout_template' => '',
			'content_template' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		),
		array(
			'id' => '3',
			'parent_id' => 2,
			'lft' => '5',
			'rght' => '6',
			'name' => 'garaphone',
			'title' => 'ガラホ',
			'sort' => '1',
			'contents_navi' => 1,
			'owner_id' => 1,
			'layout_template' => '',
			'content_template' => '',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		),
	);

}
