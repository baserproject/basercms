<?php

/**
 * UserFixture
 *
 */
class UserFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array('connection' => 'baser');

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null),
		'password' => array('type' => 'string', 'null' => true, 'default' => null),
		'real_name_1' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'real_name_2' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'email' => array('type' => 'string', 'null' => true, 'default' => null),
		'user_group_id' => array('type' => 'integer', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'name' => 'admin',
			'password' => 'ef02a5fb087587235ff1e2c69b18805a62e01042',
			'real_name_1' => '畑本',
			'real_name_2' => '寛治',
			'email' => 'kanjihtmt@gmail.com',
			'user_group_id' => 1,
			'created' => '2012-11-04 02:47:11',
			'modified' => '2012-11-04 04:50:59'
		),
		array(
			'id' => 5,
			'name' => 'aaa',
			'password' => 'ec650573d116c0639106665e4014024cec7b7809',
			'real_name_1' => 'bbb',
			'real_name_2' => 'ccc',
			'email' => 'hoge@example.com',
			'user_group_id' => 1,
			'created' => '2012-11-04 14:23:56',
			'modified' => '2012-11-04 14:23:56'
		),
		array(
			'id' => 6,
			'name' => 'bbb',
			'password' => 'ce05e4ad31b575bc9be8af012ccbc368b95fae1a',
			'real_name_1' => 'ああああ',
			'real_name_2' => 'いいいい',
			'email' => 'hoge@example.com',
			'user_group_id' => 2,
			'created' => '2012-11-20 23:17:00',
			'modified' => '2012-11-20 23:17:00'
		),
	);

}
