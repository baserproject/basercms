<?php

/* MailConfigs schema generated on: 2010-11-04 18:11:13 : 1288863013 */

class MailConfigsSchema extends CakeSchema
{

	public $name = 'MailConfigs';

	public $file = 'mail_configs.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
	}

	public $mail_configs = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
		'site_name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'site_url' => ['type' => 'string', 'null' => true, 'default' => null],
		'site_email' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'site_tel' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 20],
		'site_fax' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 20],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
