<?php

/* Permissions schema generated on: 2010-11-04 18:11:09 : 1288863009 */

class PermissionsSchema extends CakeSchema {

	public $name = 'Permissions';

	public $file = 'permissions.php';

	public $connection = 'baser';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $permissions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'no' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'name' => array('type' => 'string', 'null' => true, 'default' => null),
		'user_group_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'url' => array('type' => 'string', 'null' => true, 'default' => null),
		'auth' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
