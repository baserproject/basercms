<?php 
/* SVN FILE: $Id$ */
/* ContactMessages schema generated on: 2010-11-04 18:11:12 : 1288863012*/
class ContactMessagesSchema extends CakeSchema {
	var $name = 'ContactMessages';

	var $file = 'contact_messages.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $contact_messages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name_1' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'name_2' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'name_kana_1' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'name_kana_2' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'sex' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'email_1' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'email_2' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'tel_1' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'tel_2' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'tel_3' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'zip' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'address_1' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'address_2' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'address_3' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'category' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'message' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'root' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'root_etc' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
