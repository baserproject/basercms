<?php

/* EditorTemplates schema generated on: 2013-03-04 16:03:29 : 1362383729 */

class EditorTemplatesSchema extends CakeSchema {

	public $name = 'EditorTemplates';

	public $file = 'editor_templates.php';

	public $connection = 'baser';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $editor_templates = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50),
		'image' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'description' => array('type' => 'string', 'null' => false, 'default' => null),
		'html' => array('type' => 'text', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
