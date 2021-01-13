<?php

/**
 * BlogTag Fixture
 */
class BlogTagBlogTagFindCustomPramsFixture extends CakeTestFixture
{

	/**
	 * Import
	 *
	 * @var array
	 */
	public $import = ['model' => 'Blog.BlogTag'];

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => '1',
			'name' => 'タグ１',
			'created' => '2017-07-08 13:10:10',
			'modified' => '2017-07-09 19:41:12'
		],
		[
			'id' => '2',
			'name' => 'タグ２',
			'created' => '2017-07-09 19:41:20',
			'modified' => '2017-07-09 19:41:20'
		],
		[
			'id' => '3',
			'name' => 'タグ３',
			'created' => '2017-07-09 19:41:30',
			'modified' => '2017-07-09 19:41:30'
		],
		[
			'id' => '4',
			'name' => 'タグ４',
			'created' => '2017-07-09 19:41:30',
			'modified' => '2017-07-09 19:41:30'
		],
		[
			'id' => '5',
			'name' => 'タグ５',
			'created' => '2017-07-09 19:41:30',
			'modified' => '2017-07-09 19:41:30'
		],
	];

}
