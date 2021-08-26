<?php

/* MailFields schema generated on: 2010-11-04 18:11:13 : 1288863013 */

class MailFieldsSchema extends CakeSchema
{

	public $name = 'MailFields';

	public $file = 'mail_fields.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $mail_fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
		'mail_content_id' => ['type' => 'integer', 'null' => true, 'default' => null],
		'no' => ['type' => 'integer', 'null' => true, 'default' => null],
		'name' => ['type' => 'string', 'null' => true, 'default' => null],
		'field_name' => ['type' => 'string', 'null' => true, 'default' => null],
		'type' => ['type' => 'string', 'null' => true, 'default' => null],
		'head' => ['type' => 'string', 'null' => true, 'default' => null],
		'attention' => ['type' => 'string', 'null' => true, 'default' => null],
		'before_attachment' => ['type' => 'string', 'null' => true, 'default' => null],
		'after_attachment' => ['type' => 'string', 'null' => true, 'default' => null],
		'source' => ['type' => 'text', 'null' => true, 'default' => null],
		'size' => ['type' => 'integer', 'null' => true, 'default' => null],
		'rows' => ['type' => 'integer', 'null' => true, 'default' => null],
		'maxlength' => ['type' => 'integer', 'null' => true, 'default' => null],
		'options' => ['type' => 'string', 'null' => true, 'default' => null],
		'auto_complete' => ['type' => 'string', 'null' => true, 'default' => null],
		'class' => ['type' => 'string', 'null' => true, 'default' => null],
		'separator' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
		'default_value' => ['type' => 'string', 'null' => true, 'default' => null],
		'description' => ['type' => 'string', 'null' => true, 'default' => null],
		'group_field' => ['type' => 'string', 'null' => true, 'default' => null],
		'group_valid' => ['type' => 'string', 'null' => true, 'default' => null],
		'valid' => ['type' => 'string', 'null' => true, 'default' => null],
		'valid_ex' => ['type' => 'string', 'null' => true, 'default' => null],
		'auto_convert' => ['type' => 'string', 'null' => true, 'default' => null],
		'not_empty' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'use_field' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'no_send' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'sort' => ['type' => 'integer', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];
}
