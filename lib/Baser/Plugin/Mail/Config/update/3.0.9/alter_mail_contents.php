<?php

/* MailContents schema generated on: 2011-08-20 02:08:54 : 1313774094 */

class MailContentsSchema extends CakeSchema {

	public $name = 'MailContents';

	public $file = 'mail_contents.php';

	public $connection = 'plugin';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $mail_contents = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100],
		'title' => ['type' => 'string', 'null' => true, 'default' => null],
		'description' => ['type' => 'text', 'null' => true, 'default' => null],
		'sender_1' => ['type' => 'string', 'null' => true, 'default' => null],
		'sender_2' => ['type' => 'string', 'null' => true, 'default' => null],
		'sender_name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'subject_user' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'subject_admin' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'layout_template' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 20],
		'form_template' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 20],
		'mail_template' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 20],
		'redirect_url' => ['type' => 'string', 'null' => true, 'default' => null],
		'status' => ['type' => 'boolean', 'null' => true, 'default' => '0'],
		'auth_captcha' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'widget_area' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 4],
		'ssl_on' => ['type' => 'boolean', 'null' => true, 'default' => '0'],
		'save_info' => ['type' => 'boolean', 'null' => true, 'default' => '1'],
		'exclude_search' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'publish_begin' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'publish_end' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci']
	];

}
