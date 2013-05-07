<?php 
/* SVN FILE: $Id$ */
/* BlogPostsBlogTags schema generated on: 2011-04-24 03:04:43 : 1303583083*/
class BlogPostsBlogTagsSchema extends CakeSchema {
	var $name = 'BlogPostsBlogTags';

	var $file = 'blog_posts_blog_tags.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $blog_posts_blog_tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'blog_post_id' => array('type' => 'integer', 'null' => false, 'length' => 8),
		'blog_tag_id' => array('type' => 'integer', 'null' => false, 'length' => 8),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
