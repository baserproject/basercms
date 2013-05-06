<?php
/* SVN FILE: $Id$ */
/* Plugins schema generated on: 2010-11-04 18:11:10 : 1288863010*/
class PluginsSchema extends CakeSchema {
	var $name = 'Plugins';

	var $file = 'plugins.php';

	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $plugins = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'version' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'db_inited' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
