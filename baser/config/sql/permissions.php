<?php 
/* SVN FILE: $Id$ */
/* Permissions schema generated on: 2010-03-19 09:03:57 : 1268958297*/
class PermissionsSchema extends CakeSchema {
	var $name = 'Permissions';

	var $path = '/Users/ryuring/Documents/Projects/basercms/baser/config/sql';

	var $file = 'permissions.php';

	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $permissions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'no' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'user_group_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'auth' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array()
	);
}
?>