<?php

/* Users schema generated on: 2013-03-23 00:03:52 : 1363966852 */

class UsersSchema extends CakeSchema {

	public $name = 'Users';

	public $file = 'users.php';

	public $connection = 'baser';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null),
		'password' => array('type' => 'string', 'null' => true, 'default' => null),
		'real_name_1' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'real_name_2' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'email' => array('type' => 'string', 'null' => true, 'default' => null),
		'user_group_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4),
		'nickname' => array('type' => 'string', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
