<?php

/* Pages schema generated on: 2013-03-23 04:03:08 : 1363981208 */

class PagesSchema extends CakeSchema {

	public $name = 'Pages';

	public $file = 'pages.php';

	public $connection = 'baser';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $pages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => null),
		'description' => array('type' => 'string', 'null' => true, 'default' => null),
		'contents' => array('type' => 'text', 'null' => true, 'default' => null),
		'page_category_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'url' => array('type' => 'text', 'null' => true, 'default' => null),
		'draft' => array('type' => 'text', 'null' => true, 'default' => null),
		'author_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'publish_begin' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'publish_end' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'exclude_search' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'code' => array('type' => 'text', 'null' => true, 'default' => null),
		'unlinked_mobile' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'unlinked_smartphone' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
