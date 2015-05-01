<?php

/* FeedConfigs schema generated on: 2010-11-04 18:11:12 : 1288863012 */

class FeedConfigsSchema extends CakeSchema {

	public $name = 'FeedConfigs';

	public $file = 'feed_configs.php';

	public $connection = 'plugin';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $feed_configs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'feed_title_index' => array('type' => 'string', 'null' => true, 'default' => null),
		'category_index' => array('type' => 'string', 'null' => true, 'default' => null),
		'template' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'display_number' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
