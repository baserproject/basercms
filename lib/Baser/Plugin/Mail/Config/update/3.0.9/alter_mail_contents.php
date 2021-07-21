<?php

/* MailContents schema generated on: 2011-08-20 02:08:54 : 1313774094 */

class MailContentsSchema extends CakeSchema {

	public $name = 'MailContents';

	public $file = 'mail_contents.php';

	public $connection = 'plugin';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $mail_contents = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'title' => array('type' => 'string', 'null' => true, 'default' => null),
		'description' => array('type' => 'text', 'null' => true, 'default' => null),
		'sender_1' => array('type' => 'string', 'null' => true, 'default' => null),
		'sender_2' => array('type' => 'string', 'null' => true, 'default' => null),
		'sender_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'subject_user' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'subject_admin' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'layout_template' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'form_template' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'mail_template' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'redirect_url' => array('type' => 'string', 'null' => true, 'default' => null),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'auth_captcha' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'widget_area' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4),
		'ssl_on' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'save_info' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'exclude_search' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'publish_begin' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'publish_end' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
	);

}
