<?php

/* Favorites schema generated on: 2012-01-20 15:01:33 : 1327040553 */

class FavoritesSchema extends CakeSchema {

	public $name = 'Favorites';

	public $file = 'favorites.php';

	public $connection = 'baser';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $favorites = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8),
		'name' => array('type' => 'string', 'null' => false, 'default' => null),
		'url' => array('type' => 'string', 'null' => false, 'default' => null),
		'sort' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
