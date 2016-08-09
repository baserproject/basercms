<?php
/**
 * ContentFolder Fixture
 */
class ContentFolderFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false, 'key' => 'primary'),
		'folder_template' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'page_template' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'folder_template' => '',
			'page_template' => '',
			'created' => '2016-08-10 02:17:28',
			'modified' => null
		),
		array(
			'id' => '2',
			'folder_template' => '',
			'page_template' => '',
			'created' => '2016-08-10 02:17:28',
			'modified' => null
		),
		array(
			'id' => '3',
			'folder_template' => '',
			'page_template' => '',
			'created' => '2016-08-10 02:17:28',
			'modified' => null
		),
	);

}
