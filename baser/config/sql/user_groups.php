<?php
/* SVN FILE: $Id$ */
/* UserGroups schema generated on: 2011-01-19 17:01:38 : 1295426558*/
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
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>