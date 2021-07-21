<?php

/* Dblogs schema generated on: 2011-06-12 11:06:13 : 1307847253 */

class DblogsSchema extends CakeSchema {

	public $name = 'Dblogs';

	public $file = 'dblogs.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $dblogs = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'name' => ['type' => 'text', 'null' => true, 'default' => null],
		'user_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
