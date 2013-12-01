<?php 
/* SVN FILE: $Id$ */
/* FeedDetails schema generated on: 2010-11-04 18:11:13 : 1288863013*/
class FeedDetailsSchema extends CakeSchema {
	var $name = 'FeedDetails';

	var $file = 'feed_details.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $feed_details = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'feed_config_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'category_filter' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'cache_time' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
