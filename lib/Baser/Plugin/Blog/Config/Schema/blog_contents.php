<?php

/* BlogContents schema generated on: 2013-03-22 21:03:08 : 1363957088 */

class BlogContentsSchema extends CakeSchema {

	public $name = 'BlogContents';

	public $file = 'blog_contents.php';

	public $connection = 'plugin';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $blog_contents = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'title' => array('type' => 'string', 'null' => true, 'default' => null),
		'description' => array('type' => 'text', 'null' => true, 'default' => null),
		'layout' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'template' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'list_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4),
		'list_direction' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'feed_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4),
		'tag_use' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'comment_use' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 2),
		'comment_approve' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 2),
		'auth_captcha' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'widget_area' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4),
		'exclude_search' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'eye_catch_size' => array('type' => 'text', 'null' => true, 'default' => null),
		'use_content' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
