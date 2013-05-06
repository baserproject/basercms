<?php 
/* SVN FILE: $Id$ */
/* PageCategories schema generated on: 2013-03-22 16:03:48 : 1363938348*/
class PageCategoriesSchema extends CakeSchema {
	var $name = 'PageCategories';

	var $file = 'page_categories.php';

	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $page_categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'contents_navi' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'owner_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'layout_template' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'content_template' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>