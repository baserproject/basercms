<?php

/* FeedDetails schema generated on: 2010-11-04 18:11:13 : 1288863013 */

class FeedDetailsSchema extends CakeSchema
{

	public $name = 'FeedDetails';

	public $file = 'feed_details.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $feed_details = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'feed_config_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'url' => ['type' => 'string', 'null' => true, 'default' => null],
		'category_filter' => ['type' => 'string', 'null' => true, 'default' => null],
		'cache_time' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 20],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
