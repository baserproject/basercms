<?php

/* FeedConfigs schema generated on: 2010-11-04 18:11:12 : 1288863012 */

class FeedConfigsSchema extends CakeSchema
{

	public $name = 'FeedConfigs';

	public $file = 'feed_configs.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $feed_configs = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'feed_title_index' => ['type' => 'string', 'null' => true, 'default' => null],
		'category_index' => ['type' => 'string', 'null' => true, 'default' => null],
		'template' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'display_number' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 3],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
