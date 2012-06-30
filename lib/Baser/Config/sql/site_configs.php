<?php 
/* SVN FILE: $Id$ */
/* SiteConfigs schema generated on: 2010-11-04 18:11:10 : 1288863010*/
class SiteConfigsSchema extends CakeSchema {
	public $name = 'SiteConfigs';

	public $file = 'site_configs.php';

	public $connection = 'baser';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $site_configs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'value' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
