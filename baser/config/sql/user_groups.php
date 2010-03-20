<?php 
/* SVN FILE: $Id$ */
/* UserGroup schema generated on: 2010-03-19 04:03:57 : 1268939997*/
class UserGroupsSchema extends CakeSchema {
	var $name = 'UserGroups';

	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $user_groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 11, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'modified' => array('type' => 'datetime', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => true),
		'indexes' => array('PRIMARY' => array('unique' => true, 'column' => 'id'))
	);
}
?>