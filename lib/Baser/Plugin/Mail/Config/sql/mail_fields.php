<?php 
/* SVN FILE: $Id$ */
/* MailFields schema generated on: 2010-11-04 18:11:13 : 1288863013*/
class MailFieldsSchema extends CakeSchema {
	var $name = 'MailFields';

	var $file = 'mail_fields.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $mail_fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'mail_content_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'no' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'field_name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'type' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'head' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'attention' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'before_attachment' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'after_attachment' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'source' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'size' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'rows' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'maxlength' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'options' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'class' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'separator' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'default_value' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'group_field' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'group_valid' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'valid' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'valid_ex' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'auto_convert' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'not_empty' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'use_field' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'no_send' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
