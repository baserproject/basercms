<?php 
/* SVN FILE: $Id$ */
/* WidgetAreas schema generated on: 2010-08-03 10:08:20 : 1280800520*/
class WidgetAreasSchema extends CakeSchema {
	var $name = 'WidgetAreas';

	var $path = '/Users/ryuring/Documents/Projects/basercms/baser/config/sql';

	var $file = 'widget_areas.php';

	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $widget_areas = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 4, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'widgets' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>