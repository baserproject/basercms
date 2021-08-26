<?php

/* UploaderConfigs schema generated on: 2011-04-16 13:04:05 : 1302929345*/

class UploaderConfigsSchema extends CakeSchema
{
	public $name = 'UploaderConfigs';

	public $file = 'uploader_configs.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $uploader_configs = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false],
		'value' => ['type' => 'text', 'null' => false],
		'created' => ['type' => 'datetime', 'null' => true],
		'modified' => ['type' => 'datetime', 'null' => true],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];
}

?>
