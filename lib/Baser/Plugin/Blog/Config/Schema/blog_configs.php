<?php

/* BlogConfigs schema generated on: 2010-11-04 18:11:11 : 1288863011 */

class BlogConfigsSchema extends CakeSchema
{

	public $name = 'BlogConfigs';

	public $file = 'blog_configs.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $blog_configs = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 2, 'key' => 'primary'],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
