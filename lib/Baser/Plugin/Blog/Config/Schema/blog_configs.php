<?php

/* BlogConfigs schema generated on: 2010-11-04 18:11:11 : 1288863011 */

class BlogConfigsSchema extends CakeSchema {

	public $name = 'BlogConfigs';

	public $file = 'blog_configs.php';

	public $connection = 'plugin';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $blog_configs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 2, 'key' => 'primary'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
