<?php

/* BlogPostsBlogTags schema generated on: 2011-04-24 03:04:43 : 1303583083 */

class BlogPostsBlogTagsSchema extends CakeSchema
{

	public $name = 'BlogPostsBlogTags';

	public $file = 'blog_posts_blog_tags.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $blog_posts_blog_tags = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'blog_post_id' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'blog_tag_id' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];
}
