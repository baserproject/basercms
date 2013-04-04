<?php 
/* SVN FILE: $Id$ */
/* EditorTemplates schema generated on: 2013-03-04 16:03:29 : 1362383729*/
class EditorTemplatesSchema extends CakeSchema {
	var $name = 'EditorTemplates';

	var $file = 'editor_templates.php';

	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $editor_templates = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50),
		'image' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50),
		'description' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'html' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>