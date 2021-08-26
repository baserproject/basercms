<?php

/* EditorTemplates schema generated on: 2013-03-04 16:03:29 : 1362383729 */

class EditorTemplatesSchema extends CakeSchema {

	public $name = 'EditorTemplates';

	public $file = 'editor_templates.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $editor_templates = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'image' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'description' => ['type' => 'string', 'null' => true, 'default' => null],
		'html' => ['type' => 'text', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
