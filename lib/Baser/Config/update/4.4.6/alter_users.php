<?php

/* Users schema generated on: 2021-04-02 21:00:00 : 1617364800 */

class UsersSchema extends CakeSchema {

	public $name = 'Users';

	public $file = 'users.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $users = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => true, 'default' => null],
		'password' => ['type' => 'string', 'null' => true, 'default' => null],
		'real_name_1' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'real_name_2' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'email' => ['type' => 'string', 'null' => true, 'default' => null],
		'user_group_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 4],
		'nickname' => ['type' => 'string', 'null' => true, 'default' => null],
		'activate_key' => ['type' => 'string', 'null' => true, 'default' => null],
		'activate_expire' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci']
	];
}
