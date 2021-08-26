<?php

/* BlogTags schema generated on: 2011-04-24 03:04:44 : 1303583084 */

class BlogTagsSchema extends CakeSchema
{

	public $name = 'BlogTags';

	public $file = 'blog_tags.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $blog_tags = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
