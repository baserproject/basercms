<?php

/* SiteConfigs schema generated on: 2010-11-04 18:11:10 : 1288863010 */

class SiteConfigsSchema extends CakeSchema {

	public $name = 'SiteConfigs';

	public $file = 'site_configs.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $site_configs = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => true, 'default' => null],
		'value' => ['type' => 'text', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
