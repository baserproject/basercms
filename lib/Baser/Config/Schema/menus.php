<?php

/* GlobalMenus schema generated on: 2010-11-04 18:11:08 : 1288863008 */

class MenusSchema extends CakeSchema {

	public $name = 'Menus';

	public $file = 'menus.php';

	public $connection = 'baser';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $menus = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'key' => 'primary'),
		'no' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'link' => array('type' => 'string', 'null' => true, 'default' => null),
		'menu_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
