<?php

/* UserGroups schema generated on: 2012-03-23 13:03:39 : 1332478179 */

class UserGroupsSchema extends CakeSchema {

	public $name = 'UserGroups';

	public $file = 'user_groups.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $user_groups = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'title' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'auth_prefix' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 20],
		'use_admin_globalmenu' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'default_favorites' => ['type' => 'text', 'null' => true, 'default' => null],
		'use_move_contents' => ['type' => 'boolean', 'null' => true, 'default' => false],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
