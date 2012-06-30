<?php 
/* SVN FILE: $Id$ */
/* Contents schema generated on: 2011-08-20 02:08:53 : 1313774093*/
class ContentsSchema extends CakeSchema {
	var $name = 'Contents';

	var $file = 'contents.php';

	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $contents = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'type' => array('type' => 'string', 'null' => true, 'length' => 100),
		'model' => array('type' => 'string', 'null' => false, 'length' => 50),
		'model_id' => array('type' => 'integer', 'null' => false, 'length' => 8),
		'category' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'detail' => array('type' => 'text', 'null' => true),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'priority' => array('type' => 'string', 'null' => true, 'length' => 3),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
