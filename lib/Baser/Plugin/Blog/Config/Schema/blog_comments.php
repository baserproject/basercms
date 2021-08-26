<?php

/* BlogComments schema generated on: 2010-11-04 18:11:11 : 1288863011 */

class BlogCommentsSchema extends CakeSchema
{

	public $name = 'BlogComments';

	public $file = 'blog_comments.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $blog_comments = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
		'blog_content_id' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'blog_post_id' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'no' => ['type' => 'integer', 'null' => true],
		'status' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'email' => ['type' => 'string', 'null' => true, 'default' => null],
		'url' => ['type' => 'string', 'null' => true, 'default' => null],
		'message' => ['type' => 'text', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
