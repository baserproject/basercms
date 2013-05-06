<?php 
/* SVN FILE: $Id$ */
/* Favorites schema generated on: 2012-01-20 15:01:33 : 1327040553*/
class FavoritesSchema extends CakeSchema {
	var $name = 'Favorites';

	var $file = 'favorites.php';

	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $favorites = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'url' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'sort' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
