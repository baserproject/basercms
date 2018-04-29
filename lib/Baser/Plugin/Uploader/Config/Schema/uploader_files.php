<?php
/* UploaderFiles schema generated on: 2011-04-18 04:04:01 : 1303067461*/
class UploaderFilesSchema extends CakeSchema {
	public $name = 'UploaderFiles';

	public $file = 'uploader_files.php';

	public $connection = 'default';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $uploader_files = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'alt' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'uploader_category_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'publish_begin' => array('type' => 'datetime', 'null' => true, 'dafault' => NULL),
		'publish_end' => array('type' => 'datetime', 'null' => true, 'dafault' => NULL),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	);
}
?>