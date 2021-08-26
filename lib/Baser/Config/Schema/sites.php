<?php
class SitesSchema extends CakeSchema {

	public $file = 'sites.php';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $sites = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false, 'key' => 'primary'],
		'main_site_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false],
		'name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'display_name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'title' => ['type' => 'string', 'null' => true, 'default' => null],
		'alias' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'theme' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'status' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'keyword' => ['type' => 'text', 'null' => true, 'default' => null],
		'description' => ['type' => 'text', 'null' => true, 'default' => null],
		'use_subdomain' => ['type' => 'boolean', 'null' => true, 'default' => '0'],
		'relate_main_site' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'device' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'lang' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'same_main_url' => ['type' => 'boolean', 'null' => true, 'default' => '0'],
		'auto_redirect' => ['type' => 'boolean', 'null' => true, 'default' => '0'],
		'auto_link' => ['type' => 'boolean', 'null' => true, 'default' => '0'],
		'domain_type' => ['type' => 'integer', 'null' => true, 'default' => 0, 'length' => 8, 'unsigned' => false],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
	];

}
