<?php 
class ContentsSchema extends CakeSchema {

	public $file = 'contents.php';

	public $connection = 'default';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $contents = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'plugin' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'entity_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'url' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'site_id' => array('type' => 'integer', 'null' => true, 'default' => 0, 'length' => 8, 'unsigned' => false),
		'alias_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'main_site_content_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'level' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'eyecatch' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'author_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'layout_template' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'publish_begin' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'publish_end' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'self_status' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'self_publish_begin' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'self_publish_end' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'exclude_search' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'created_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'site_root' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'exclude_menu' => array('type' => 'boolean', 'null' => true, 'default' => false),
		'blank_link' => array('type' => 'boolean', 'null' => true, 'default' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

}
