<?php

/* ContactMessages schema generated on: 2010-11-04 18:11:12 : 1288863012 */

class ContactMessagesSchema extends CakeSchema {

	public $name = 'ContactMessages';

	public $file = 'contact_messages.php';

	public $connection = 'plugin';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $contact_messages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'name_1' => array('type' => 'string', 'null' => true, 'default' => null),
		'name_2' => array('type' => 'string', 'null' => true, 'default' => null),
		'name_kana_1' => array('type' => 'string', 'null' => true, 'default' => null),
		'name_kana_2' => array('type' => 'string', 'null' => true, 'default' => null),
		'sex' => array('type' => 'string', 'null' => true, 'default' => null),
		'email_1' => array('type' => 'string', 'null' => true, 'default' => null),
		'email_2' => array('type' => 'string', 'null' => true, 'default' => null),
		'tel_1' => array('type' => 'string', 'null' => true, 'default' => null),
		'tel_2' => array('type' => 'string', 'null' => true, 'default' => null),
		'tel_3' => array('type' => 'string', 'null' => true, 'default' => null),
		'zip' => array('type' => 'string', 'null' => true, 'default' => null),
		'address_1' => array('type' => 'string', 'null' => true, 'default' => null),
		'address_2' => array('type' => 'string', 'null' => true, 'default' => null),
		'address_3' => array('type' => 'string', 'null' => true, 'default' => null),
		'category' => array('type' => 'string', 'null' => true, 'default' => null),
		'message' => array('type' => 'text', 'null' => true, 'default' => null),
		'root' => array('type' => 'string', 'null' => true, 'default' => null),
		'root_etc' => array('type' => 'string', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);

}
