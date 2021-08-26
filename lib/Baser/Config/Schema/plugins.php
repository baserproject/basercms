<?php

/* Plugins schema generated on: 2010-11-04 18:11:10 : 1288863010 */

class PluginsSchema extends CakeSchema {

	public $name = 'Plugins';

	public $file = 'plugins.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $plugins = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'title' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'version' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100],
		'status' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'db_inited' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'priority' => ['type' => 'integer', 'null' => true, 'default' => 0, 'length' => 8],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
