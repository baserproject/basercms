<?php

/**
 * ContentFixture
 */
class ContentIsMovableFixture extends BaserTestFixture
{

	/**
	 * Name of the object
	 *
	 * @var string
	 */
	public $name = 'Content';

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => '1',
			'type' => 'ContentFolder',
			'name' => '',
			'title' => 'トップディレクトリ',
			'entity_id' => '1',
			'url' => '/',
			'parent_id' => null,
			'lft' => '1',
			'rght' => '2',
			'level' => 0,
			'site_id' => '0',
			'site_root' => 1,
			'plugin' => 'Core',
			'deleted' => 0
		],
		[
			'id' => '2',
			'type' => 'Page',
			'name' => 'index',
			'title' => 'トップ',
			'entity_id' => '1',
			'url' => '/index',
			'parent_id' => 1,
			'lft' => '2',
			'rght' => '3',
			'level' => 0,
			'site_id' => '0',
			'site_root' => 0,
			'plugin' => 'Core',
			'deleted' => 0
		],
		[
			'id' => '3',
			'type' => 'ContentFolder',
			'name' => 'about',
			'title' => '会社案内',
			'entity_id' => '2',
			'url' => '/about/',
			'parent_id' => 1,
			'lft' => '4',
			'rght' => '7',
			'level' => 0,
			'site_id' => '0',
			'plugin' => 'Core',
			'site_root' => 0,
			'deleted' => 0
		],
		[
			'id' => '4',
			'type' => 'Page',
			'name' => 'index',
			'title' => '会社案内',
			'entity_id' => '2',
			'url' => '/about/index',
			'parent_id' => 3,
			'lft' => '5',
			'rght' => '6',
			'level' => 1,
			'site_id' => '0',
			'plugin' => 'Core',
			'site_root' => 0,
			'deleted' => 0
		],
		[
			'id' => '5',
			'type' => 'ContentFolder',
			'name' => 'service',
			'title' => 'サービス',
			'entity_id' => '3',
			'url' => '/service/',
			'parent_id' => 1,
			'lft' => '8',
			'rght' => '11',
			'level' => 0,
			'site_id' => '0',
			'plugin' => 'Core',
			'site_root' => 0,
			'deleted' => 0
		],
		[
			'id' => '6',
			'type' => 'ContentFolder',
			'name' => 'index',
			'title' => 'サービストップディレクトリ',
			'entity_id' => '3',
			'url' => '/service/index/',
			'parent_id' => 5,
			'lft' => '9',
			'rght' => '10',
			'level' => 1,
			'site_id' => '0',
			'plugin' => 'Core',
			'site_root' => 0,
			'deleted' => 0
		],
		[
			'id' => '7',
			'type' => 'ContentFolder',
			'name' => 'item',
			'title' => '商品ディレクトリ',
			'entity_id' => '4',
			'url' => '/item/',
			'parent_id' => 1,
			'lft' => '12',
			'rght' => '15',
			'level' => 0,
			'site_id' => '0',
			'plugin' => 'Core',
			'site_root' => 0,
			'deleted' => 0
		],
		[
			'id' => '8',
			'type' => 'ContentFolder',
			'name' => 'service',
			'title' => '商品のサービス',
			'entity_id' => '5',
			'url' => '/item/service/',
			'parent_id' => 7,
			'lft' => '13',
			'rght' => '14',
			'level' => 1,
			'site_id' => '0',
			'plugin' => 'Core',
			'site_root' => 0,
			'deleted' => 0
		],
		[
			'id' => '9',
			'type' => 'ContentFolder',
			'name' => 'sp',
			'title' => 'スマホ',
			'entity_id' => '6',
			'url' => '/sp/',
			'parent_id' => 1,
			'lft' => '16',
			'rght' => '21',
			'level' => 1,
			'site_id' => '2',
			'plugin' => 'Core',
			'site_root' => 1,
			'deleted' => 0
		],
		[
			'id' => '10',
			'type' => 'ContentFolder',
			'name' => 'item',
			'title' => 'スマホの商品',
			'entity_id' => '7',
			'url' => '/s/item/',
			'parent_id' => 9,
			'lft' => '17',
			'rght' => '20',
			'level' => 2,
			'site_id' => '2',
			'plugin' => 'Core',
			'site_root' => 0,
			'deleted' => 0
		],
		[
			'id' => '11',
			'type' => 'Page',
			'name' => 'index',
			'title' => 'スマホの商品のインデックス',
			'entity_id' => '3',
			'url' => '/s/item/index',
			'parent_id' => 10,
			'lft' => '18',
			'rght' => '19',
			'level' => 3,
			'site_id' => '2',
			'plugin' => 'Core',
			'site_root' => 0,
			'deleted' => 0
		],
	];

}
