<?php 
/* SVN FILE: $Id$ */
/* MailContents schema generated on: 2011-08-20 02:08:54 : 1313774094*/
class MailContentsSchema extends CakeSchema {
	var $name = 'MailContents';

	var $file = 'mail_contents.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $mail_contents = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'sender_1' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'sender_2' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'sender_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'subject_user' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'subject_admin' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'layout_template' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'form_template' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'mail_template' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'redirect_url' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'auth_captcha' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'widget_area' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'ssl_on' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'exclude_search' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
