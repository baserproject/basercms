<?php

/* UploaderFiles schema generated on: 2011-04-18 04:04:01 : 1303067461*/

class UploaderFilesSchema extends CakeSchema
{
	public $name = 'UploaderFiles';

	public $file = 'uploader_files.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $uploader_files = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => true, 'default' => NULL],
		'alt' => ['type' => 'text', 'null' => true, 'default' => NULL],
		'uploader_category_id' => ['type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8],
		'user_id' => ['type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8],
		'publish_begin' => ['type' => 'datetime', 'null' => true, 'dafault' => NULL],
		'publish_end' => ['type' => 'datetime', 'null' => true, 'dafault' => NULL],
		'created' => ['type' => 'datetime', 'null' => true],
		'modified' => ['type' => 'datetime', 'null' => true],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];
}

?>
