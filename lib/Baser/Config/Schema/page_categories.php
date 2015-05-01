<?php

/* PageCategories schema generated on: 2013-03-22 16:03:48 : 1363938348 */

class PageCategoriesSchema extends CakeSchema {

	public $name = 'PageCategories';

	public $file = 'page_categories.php';

	public $connection = 'baser';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $page_categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => null),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'contents_navi' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'owner_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'layout_template' => array('type' => 'string', 'null' => true, 'default' => null),
		'content_template' => array('type' => 'string', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
