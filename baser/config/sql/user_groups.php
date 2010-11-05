<?php 
/* SVN FILE: $Id$ */
/* UserGroups schema generated on: 2010-11-04 18:11:10 : 1288863010*/
class UserGroupsSchema extends CakeSchema {
	var $name = 'UserGroups';

	var $path = '/Users/ryuring/Documents/Projects/basercms/app/tmp/schemas/';

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
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>