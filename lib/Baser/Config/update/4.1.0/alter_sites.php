<?php 
class SitesSchema extends CakeSchema {

	public $file = 'sites.php';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $sites = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false, 'key' => 'primary'),
		'main_site_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'display_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => null),
		'alias' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'theme' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'keyword' => array('type' => 'text', 'null' => true, 'default' => null),
		'description' => array('type' => 'text', 'null' => true, 'default' => null),
		'use_subdomain' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'relate_main_site' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'device' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'lang' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'same_main_url' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'auto_redirect' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'auto_link' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'domain_type' => array('type' => 'integer', 'null' => true, 'default' => 0, 'length' => 8, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
	);

}
