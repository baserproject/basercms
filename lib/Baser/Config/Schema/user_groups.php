<?php

/* UserGroups schema generated on: 2012-03-23 13:03:39 : 1332478179 */

class UserGroupsSchema extends CakeSchema {

	public $name = 'UserGroups';

	public $file = 'user_groups.php';

	public $connection = 'baser';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $user_groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'auth_prefix' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'use_admin_globalmenu' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'default_favorites' => array('type' => 'text', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
