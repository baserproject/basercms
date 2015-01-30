<?php
/**
 * MenuFixture
 *
 */
class MenuFixture extends CakeTestFixture {

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
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false, 'key' => 'primary'),
		'no' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => false),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'link' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'menu_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => false),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => null),
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
			'no' => '1',
			'name' => 'ホーム',
			'link' => '/',
			'menu_type' => 'default',
			'sort' => '1',
			'status' => 1,
			'created' => '2015-01-27 12:56:52',
			'modified' => null
		),
		array(
			'id' => '2',
			'no' => '2',
			'name' => '会社案内',
			'link' => '/company',
			'menu_type' => 'default',
			'sort' => '2',
			'status' => 1,
			'created' => '2015-01-27 12:56:52',
			'modified' => null
		),
		array(
			'id' => '3',
			'no' => '3',
			'name' => 'サービス',
			'link' => '/service',
			'menu_type' => 'default',
			'sort' => '3',
			'status' => 1,
			'created' => '2015-01-27 12:56:52',
			'modified' => null
		),
		array(
			'id' => '4',
			'no' => '4',
			'name' => '新着情報',
			'link' => '/news/index',
			'menu_type' => 'default',
			'sort' => '4',
			'status' => 1,
			'created' => '2015-01-27 12:56:52',
			'modified' => null
		),
		array(
			'id' => '5',
			'no' => '5',
			'name' => 'お問い合わせ',
			'link' => '/contact/index',
			'menu_type' => 'default',
			'sort' => '5',
			'status' => 1,
			'created' => '2015-01-27 12:56:52',
			'modified' => null
		),
		array(
			'id' => '6',
			'no' => '6',
			'name' => '採用情報',
			'link' => '/recruit',
			'menu_type' => 'default',
			'sort' => '7',
			'status' => 1,
			'created' => '2015-01-27 12:56:52',
			'modified' => null
		),
	);

}
