<?php

/* BlogPostsBlogTags schema generated on: 2011-04-24 03:04:43 : 1303583083 */

class BlogPostsBlogTagsSchema extends CakeSchema {

	public $name = 'BlogPostsBlogTags';

	public $file = 'blog_posts_blog_tags.php';

	public $connection = 'plugin';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $blog_posts_blog_tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'blog_post_id' => array('type' => 'integer', 'null' => false, 'length' => 8),
		'blog_tag_id' => array('type' => 'integer', 'null' => false, 'length' => 8),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
