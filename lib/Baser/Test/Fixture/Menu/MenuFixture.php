<?php
/**
 * MenuFixture
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

class MenuFixture extends CakeTestFixture {

/**
* Import
*
* @var array
*/
	public $import = array( 
		'connection'	=> 'test'
	);
/**
 * Fields
 * 
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'key' => 'primary'),
		'no' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'link' => array('type' => 'string', 'null' => true, 'default' => null),
		'menu_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1', 
			'no' => '1',
			'name' => 'ホーム',
			'link' => '/',
			'menu_type' => 'default',
			'sort' => '1',
			'status' => true,
			'created' => '2014-09-02 14:10:21',
			'modified' => null
		),
		array(
			'id' => '2',
			'no' => '2',
			'name' => '会社案内',
			'link' => '/about',
			'menu_type' => 'default',
			'sort' => '2',
			'status' => true,
			'created' => '2014-09-02 14:10:21',
			'modified' => null
		),
        array(
			'id' => '3',
			'no' => '3',
			'name' => 'サービス',
			'link' => '/service',
			'menu_type' => 'default',
			'sort' => '3',
			'status' => true,
			'created' => '2014-09-02 14:10:21',
			'modified' => null
		),
        array(
			'id' => '4',
			'no' => '4',
			'name' => '新着情報',
			'link' => '/news/index',
			'menu_type' => 'default',
			'sort' => '4',
			'status' => true,
			'created' => '2014-09-02 14:10:21',
			'modified' => null
		),
		array(
			'id' => '5',
			'no' => '5',
			'name' => 'お問い合わせ',
			'link' => '/contact/index',
			'menu_type' => 'default',
			'sort' => '5',
			'status' => true,
			'created' => '2014-09-02 14:10:21',
			'modified' => null
		),
        array(
			'id' => '7',
			'no' => '7',
			'name' => 'アイコンの使い方',
			'link' => '/icons',
			'menu_type' => '',
			'sort' => '6',
			'status' => true,
			'created' => '2014-09-02 14:10:21',
			'modified' => null
		),
        array(
          'id' => '6',
          'no' => '6',
          'name' => 'サイトマップ',
          'link' => '/sitemap',
          'menu_type' => 'default',
          'sort' => '7',
          'status' => true,
          'created' => '2014-09-02 14:10:21',
          'modified' => null
		)
	);
}