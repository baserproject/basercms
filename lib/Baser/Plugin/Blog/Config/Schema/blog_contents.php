<?php

/* BlogContents schema generated on: 2013-03-22 21:03:08 : 1363957088 */

class BlogContentsSchema extends CakeSchema
{

	public $name = 'BlogContents';

	public $file = 'blog_contents.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $blog_contents = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'description' => ['type' => 'text', 'null' => true, 'default' => null],
		'template' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 20],
		'list_count' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 4],
		'list_direction' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 4],
		'feed_count' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 4],
		'tag_use' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'comment_use' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'comment_approve' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'auth_captcha' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'widget_area' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 4],
		'eye_catch_size' => ['type' => 'text', 'null' => true, 'default' => null],
		'use_content' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
