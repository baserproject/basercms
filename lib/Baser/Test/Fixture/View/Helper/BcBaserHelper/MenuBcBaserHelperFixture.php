<?php
/**
 * MenuFixture
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

class MenuBcBaserHelperFixture extends BaserTestFixture {

/**
 * Name of the object
 *
 * @var string
 */
	public $name = 'Menu';

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