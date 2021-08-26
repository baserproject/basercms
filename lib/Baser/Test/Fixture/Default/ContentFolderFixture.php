<?php

/**
 * ContentFolder Fixture
 */
class ContentFolderFixture extends CakeTestFixture
{

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false, 'key' => 'primary'],
		'folder_template' => ['type' => 'string', 'null' => true, 'default' => null],
		'page_template' => ['type' => 'string', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
	];

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => '1',
			'folder_template' => '',
			'page_template' => '',
			'created' => '2016-08-10 02:17:28',
			'modified' => null
		],
		[
			'id' => '2',
			'folder_template' => '',
			'page_template' => '',
			'created' => '2016-08-10 02:17:28',
			'modified' => null
		],
		[
			'id' => '3',
			'folder_template' => '',
			'page_template' => '',
			'created' => '2016-08-10 02:17:28',
			'modified' => null
		],
	];
}
