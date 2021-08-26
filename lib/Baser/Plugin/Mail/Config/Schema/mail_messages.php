<?php

/* MailMessages schema generated on: 2010-11-04 18:11:13 : 1288863013 */

class MailMessagesSchema extends CakeSchema
{

	public $name = 'MailMessages';

	public $file = 'mail_messages.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $mail_messages = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
