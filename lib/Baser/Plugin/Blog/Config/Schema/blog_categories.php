<?php

/* BlogCategories schema generated on: 2010-11-04 18:11:11 : 1288863011 */

class BlogCategoriesSchema extends CakeSchema
{

	public $name = 'BlogCategories';

	public $file = 'blog_categories.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $blog_categories = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'blog_content_id' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'no' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'title' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'status' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'owner_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
