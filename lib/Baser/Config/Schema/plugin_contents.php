<?php

/* PluginContents schema generated on: 2010-11-04 18:11:09 : 1288863009 */

class PluginContentsSchema extends CakeSchema {

	public $name = 'PluginContents';

	public $file = 'plugin_contents.php';

	public $connection = 'baser';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $plugin_contents = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'content_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'plugin' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
