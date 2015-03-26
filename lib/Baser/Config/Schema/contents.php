<?php

/* Contents schema generated on: 2011-08-20 02:08:53 : 1313774093 */

class ContentsSchema extends CakeSchema {

	public $name = 'Contents';

	public $file = 'contents.php';

	public $connection = 'baser';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $contents = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'type' => array('type' => 'string', 'null' => true, 'length' => 100),
		'model' => array('type' => 'string', 'null' => false, 'length' => 50),
		'model_id' => array('type' => 'integer', 'null' => false, 'length' => 8),
		'category' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => null),
		'detail' => array('type' => 'text', 'null' => true),
		'url' => array('type' => 'string', 'null' => true, 'default' => null),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'priority' => array('type' => 'string', 'null' => true, 'length' => 3),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
