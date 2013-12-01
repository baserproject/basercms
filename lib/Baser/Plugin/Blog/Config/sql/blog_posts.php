<?php 
/* SVN FILE: $Id$ */
/* BlogPosts schema generated on: 2011-08-20 02:08:54 : 1313774094*/
class BlogPostsSchema extends CakeSchema {
	var $name = 'BlogPosts';

	var $file = 'blog_posts.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $blog_posts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'blog_content_id' => array('type' => 'integer', 'null' => false, 'length' => 8),
		'no' => array('type' => 'integer', 'null' => false),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'content' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'detail' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'blog_category_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'status' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2),
		'posts_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'content_draft' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'detail_draft' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'publish_begin' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'publish_end' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'exclude_search' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'eye_catch' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 30),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
