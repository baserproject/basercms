<?php 
/* SVN FILE: $Id$ */
/* BlogContents schema generated on: 2011-08-20 02:08:54 : 1313774094*/
class BlogContentsSchema extends CakeSchema {
	var $name = 'BlogContents';

	var $file = 'blog_contents.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $blog_contents = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'layout' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'template' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'list_count' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'list_direction' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 4),
		'feed_count' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'tag_use' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'comment_use' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2),
		'comment_approve' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2),
		'auth_captcha' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'widget_area' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'exclude_search' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>