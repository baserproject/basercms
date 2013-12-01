<?php 
/* SVN FILE: $Id$ */
/* Messages schema generated on: 2010-11-04 18:11:13 : 1288863013*/
class MessagesSchema extends CakeSchema {
	var $name = 'Messages';

	var $file = 'messages.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $messages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
