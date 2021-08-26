<?php

/* Permissions schema generated on: 2010-11-04 18:11:09 : 1288863009 */

class PermissionsSchema extends CakeSchema {

	public $name = 'Permissions';

	public $file = 'permissions.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $permissions = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'no' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'sort' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'name' => ['type' => 'string', 'null' => true, 'default' => null],
		'user_group_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'url' => ['type' => 'string', 'null' => true, 'default' => null],
		'auth' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'status' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
