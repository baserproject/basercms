<?php
/**
 * PluginFixture
 *
 */
class PluginFixture extends CakeTestFixture {

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
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'version' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'db_inited' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 8, 'unsigned' => false),
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
			'name' => 'Blog',
			'title' => 'ブログ',
			'version' => '3.0.6.1',
			'status' => 1,
			'db_inited' => 1,
			'priority' => '1',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		),
		array(
			'id' => '2',
			'name' => 'Feed',
			'title' => 'フィードリーダー',
			'version' => '3.0.6.1',
			'status' => 1,
			'db_inited' => 1,
			'priority' => '2',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		),
		array(
			'id' => '3',
			'name' => 'Mail',
			'title' => 'メールフォーム',
			'version' => '3.0.6.1',
			'status' => 1,
			'db_inited' => 1,
			'priority' => '3',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		),
	);

}
