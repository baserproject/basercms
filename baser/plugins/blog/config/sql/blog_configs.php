<?php 
/* SVN FILE: $Id$ */
/* BlogConfigs schema generated on: 2010-11-04 18:11:11 : 1288863011*/
class BlogConfigsSchema extends CakeSchema {
	var $name = 'BlogConfigs';

	var $file = 'blog_configs.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $blog_configs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 2, 'key' => 'primary'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
