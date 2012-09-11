<?php 
/* SVN FILE: $Id$ */
/* UserGroups schema generated on: 2012-03-23 13:03:39 : 1332478179*/
class UserGroupsSchema extends CakeSchema {
	var $name = 'UserGroups';

	var $file = 'user_groups.php';

	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $user_groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'auth_prefix' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'use_admin_globalmenu' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'default_favorites' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
