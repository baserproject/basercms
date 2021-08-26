<?php

/* Favorites schema generated on: 2012-01-20 15:01:33 : 1327040553 */

class FavoritesSchema extends CakeSchema {

	public $name = 'Favorites';

	public $file = 'favorites.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $favorites = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'user_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'name' => ['type' => 'string', 'null' => true, 'default' => null],
		'url' => ['type' => 'string', 'null' => true, 'default' => null],
		'sort' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
