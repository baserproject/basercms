<?php

/* UploaderCategories schema generated on: 2011-04-18 04:04:01 : 1303067461*/

class UploaderCategoriesSchema extends CakeSchema
{
	public $name = 'UploaderCategories';

	public $file = 'uploader_categories.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $uploader_categories = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50],
		'modified' => ['type' => 'datetime', 'null' => true],
		'created' => ['type' => 'datetime', 'null' => true],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];
}

?>
