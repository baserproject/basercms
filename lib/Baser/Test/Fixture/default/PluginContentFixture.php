<?php
/**
 * PluginContentFixture
 *
 */
class PluginContentFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array('records' => true, 'connection' => 'baser');

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'content_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'plugin' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
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
			'content_id' => '1',
			'name' => 'news',
			'plugin' => 'blog',
			'created' => '2015-01-27 12:56:52',
			'modified' => null
		),
		array(
			'id' => '2',
			'content_id' => '1',
			'name' => 'contact',
			'plugin' => 'mail',
			'created' => '2015-01-27 12:56:52',
			'modified' => null
		),
	);

}
