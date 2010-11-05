<?php 
/* SVN FILE: $Id$ */
/* Dblogs schema generated on: 2010-11-04 18:11:08 : 1288863008*/
class DblogsSchema extends CakeSchema {
	var $name = 'Dblogs';

	var $path = '/Users/ryuring/Documents/Projects/basercms/app/tmp/schemas/';

	var $file = 'dblogs.php';

	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $dblogs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>