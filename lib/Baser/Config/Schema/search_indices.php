<?php

/* SearchIndices schema generated on: 2011-08-20 02:08:53 : 1313774093 */

class SearchIndicesSchema extends CakeSchema {

	public $name = 'SearchIndices';

	public $file = 'search_indices.php';

	public $connection = 'default';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $search_indices = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'type' => array('type' => 'string', 'null' => true, 'length' => 100),
		'model' => array('type' => 'string', 'null' => true, 'length' => 50),
		'model_id' => array('type' => 'integer', 'null' => true, 'length' => 8),
		'site_id' => array('type' => 'integer', 'null' => true, 'length' => 8),
		'content_id' => array('type' => 'integer', 'null' => true, 'length' => 8),
		'content_filter_id' => array('type' => 'integer', 'null' => true, 'length' => 8),
		'lft' => array('type' => 'integer', 'null' => true, 'length' => 8),
		'rght' => array('type' => 'integer', 'null' => true, 'length' => 8),
		'title' => array('type' => 'string', 'null' => true, 'default' => null),
		'detail' => array('type' => 'text', 'null' => true),
		'url' => array('type' => 'string', 'null' => true, 'default' => null),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'priority' => array('type' => 'string', 'null' => true, 'length' => 3),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
