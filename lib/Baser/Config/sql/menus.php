<?php

/* SVN FILE: $Id$ */
/* GlobalMenus schema generated on: 2010-11-04 18:11:08 : 1288863008 */

class MenusSchema extends CakeSchema {

	var $name = 'Menus';
	var $file = 'menus.php';
	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
		
	}

	var $menus = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 3, 'key' => 'primary'),
		'no' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 3),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'link' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'menu_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 3),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);

}
