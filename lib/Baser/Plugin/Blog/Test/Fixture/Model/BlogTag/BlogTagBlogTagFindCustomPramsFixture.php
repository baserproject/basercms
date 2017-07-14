<?php
/**
 * BlogTag Fixture
 */
class BlogTagBlogTagFindCustomPramsFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array('model' => 'Blog.BlogTag');

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'name' => 'タグ１',
			'created' => '2017-07-08 13:10:10',
			'modified' => '2017-07-09 19:41:12'
		),
		array(
			'id' => '2',
			'name' => 'タグ２',
			'created' => '2017-07-09 19:41:20',
			'modified' => '2017-07-09 19:41:20'
		),
		array(
			'id' => '3',
			'name' => 'タグ３',
			'created' => '2017-07-09 19:41:30',
			'modified' => '2017-07-09 19:41:30'
		),
		array(
			'id' => '4',
			'name' => 'タグ４',
			'created' => '2017-07-09 19:41:30',
			'modified' => '2017-07-09 19:41:30'
		),
		array(
			'id' => '5',
			'name' => 'タグ５',
			'created' => '2017-07-09 19:41:30',
			'modified' => '2017-07-09 19:41:30'
		),
	);

}
