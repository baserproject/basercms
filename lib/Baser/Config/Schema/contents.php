<?php
class ContentsSchema extends CakeSchema {

	public $file = 'contents.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $contents = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'text', 'null' => true, 'default' => null],
		'plugin' => ['type' => 'string', 'null' => true, 'default' => null],
		'type' => ['type' => 'string', 'null' => true, 'default' => null],
		'entity_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false],
		'url' => ['type' => 'text', 'null' => true, 'default' => null],
		'site_id' => ['type' => 'integer', 'null' => true, 'default' => 0, 'length' => 8, 'unsigned' => false],
		'alias_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false],
		'main_site_content_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false],
		'level' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false],
		'title' => ['type' => 'string', 'null' => true, 'default' => null],
		'description' => ['type' => 'text', 'null' => true, 'default' => null],
		'eyecatch' => ['type' => 'string', 'null' => true, 'default' => null],
		'author_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false],
		'layout_template' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'status' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'publish_begin' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'publish_end' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'self_status' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'self_publish_begin' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'self_publish_end' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'exclude_search' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'created_date' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified_date' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'site_root' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'deleted_date' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'deleted' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'exclude_menu' => ['type' => 'boolean', 'null' => true, 'default' => false],
		'blank_link' => ['type' => 'boolean', 'null' => true, 'default' => false],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
	];

}
