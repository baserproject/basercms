<?php
//
// Database Configuration File created by baserCMS Installation
//
class DATABASE_CONFIG {
public $baser = array(
	'datasource' => 'Database/BcMysql',
	'persistent' => false,
	'host' => 'localhost',
	'port' => '3306',
	'login' => 'root',
	'password' => 'root',
	'database' => 'basercms',
	'schema' => '',
	'prefix' => 'mysite_',
	'encoding' => 'utf8'
);
public $plugin = array(
	'datasource' => 'Database/BcMysql',
	'persistent' => false,
	'host' => 'localhost',
	'port' => '3306',
	'login' => 'root',
	'password' => 'root',
	'database' => 'basercms',
	'schema' => '',
	'prefix' => 'mysite_pg_',
	'encoding' => 'utf8'
);
public $test = array(
	'datasource' => 'Database/BcMysql',
	'persistent' => false,
	'host' => 'localhost',
	'port' => '3306',
	'login' => 'root',
	'password' => 'root',
	'database' => 'basercms',
	'schema' => '',
	'prefix' => 'mysite_test_',
	'encoding' => 'utf8'
);
}
