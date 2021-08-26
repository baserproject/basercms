<?php

/* MailContents schema generated on: 2011-08-20 02:08:54 : 1313774094 */

class MailContentsSchema extends CakeSchema
{

	public $name = 'MailContents';

	public $file = 'mail_contents.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $mail_contents = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'description' => ['type' => 'text', 'null' => true, 'default' => null],
		'sender_1' => ['type' => 'text', 'null' => true, 'default' => null],
		'sender_2' => ['type' => 'text', 'null' => true, 'default' => null],
		'sender_name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
		'subject_user' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
		'subject_admin' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
		'form_template' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 20],
		'mail_template' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 20],
		'redirect_url' => ['type' => 'string', 'null' => true, 'default' => null],
		'auth_captcha' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'widget_area' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 4],
		'ssl_on' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'save_info' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'publish_begin' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'publish_end' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
