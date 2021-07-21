<?php
class SearchIndicesSchema extends CakeSchema {

	public $name = 'SearchIndices';

	public $file = 'search_indices.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $search_indices = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'type' => ['type' => 'string', 'null' => true, 'length' => 100],
		'model' => ['type' => 'string', 'null' => true, 'length' => 50],
		'model_id' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'site_id' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'content_id' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'content_filter_id' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'lft' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'rght' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'title' => ['type' => 'string', 'null' => true, 'default' => null],
		'detail' => ['type' => 'text', 'null' => true],
		'url' => ['type' => 'text', 'null' => true, 'default' => null],
		'status' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'priority' => ['type' => 'string', 'null' => true, 'length' => 3],
		'publish_begin' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'publish_end' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true],
		'modified' => ['type' => 'datetime', 'null' => true],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
