<?php 
/* SVN FILE: $Id$ */
/* BlogComments schema generated on: 2010-11-04 18:11:11 : 1288863011*/
class BlogCommentsSchema extends CakeSchema {
	var $name = 'BlogComments';

	var $file = 'blog_comments.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $blog_comments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'blog_content_id' => array('type' => 'integer', 'null' => false, 'length' => 8),
		'blog_post_id' => array('type' => 'integer', 'null' => false, 'length' => 8),
		'no' => array('type' => 'integer', 'null' => false),
		'status' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'email' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'message' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
