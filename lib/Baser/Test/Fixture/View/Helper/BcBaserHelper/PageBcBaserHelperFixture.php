<?php

class PageBcBaserHelperFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array('model' => 'Page', 'connection' => 'baser');
	
/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false, 'key' => 'primary'),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'contents' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'page_category_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'url' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'draft' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'author_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'publish_begin' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'publish_end' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'exclude_search' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'code' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'unlinked_mobile' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'unlinked_smartphone' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id'			=> 1,
			'sort'			=> 1,
			'name'			=> 'index',
			'title'			=> 'PCトップページ',
			'description'	=> '',
			'contents'		=> '',
			'page_category_id'	=> '',
			'status'			=> 1,
			'url'				=> '/index',
			'draft'				=> '',
			'author_id'			=> 1,
			'publish_begin'		=> '',
			'publish_end'		=> '',
			'exclude_search'	=> false,
			'code'				=> '',
			'unlinked_mobile'	=> false,
			'unlinked_smartphone'	=> false,
			'modified'			=> '',
			'created'			=> '2014-10-20'
		),
		array(
			'id'			=> 2,
			'sort'			=> 2,
			'name'			=> 'service',
			'title'			=> 'サービス',
			'description'	=> '',
			'contents'		=> '',
			'page_category_id'	=> '',
			'status'			=> 1,
			'url'				=> '/service',
			'draft'				=> '',
			'author_id'			=> 1,
			'publish_begin'		=> '',
			'publish_end'		=> '',
			'exclude_search'	=> false,
			'code'				=> '',
			'unlinked_mobile'	=> false,
			'unlinked_smartphone'	=> false,
			'modified'			=> '',
			'created'			=> '2014-10-20'
		),
		array(
			'id'			=> 3,
			'sort'			=> 3,
			'name'			=> 'company',
			'title'			=> '会社案内',
			'description'	=> '',
			'contents'		=> '',
			'page_category_id'	=> '',
			'status'			=> 1,
			'url'				=> '/company',
			'draft'				=> '',
			'author_id'			=> 1,
			'publish_begin'		=> '',
			'publish_end'		=> '',
			'exclude_search'	=> false,
			'code'				=> '',
			'unlinked_mobile'	=> false,
			'unlinked_smartphone'	=> false,
			'modified'			=> '',
			'created'			=> '2014-10-20'
		),
		array(
			'id'			=> 4,
			'sort'			=> 4,
			'name'			=> 'recruit',
			'title'			=> '採用情報',
			'description'	=> '',
			'contents'		=> '',
			'page_category_id'	=> '',
			'status'			=> 1,
			'url'				=> '/recruit',
			'draft'				=> '',
			'author_id'			=> 1,
			'publish_begin'		=> '',
			'publish_end'		=> '',
			'exclude_search'	=> false,
			'code'				=> '',
			'unlinked_mobile'	=> false,
			'unlinked_smartphone'	=> false,
			'modified'			=> '',
			'created'			=> '2014-10-20'
		),
		array(
			'id'			=> 5,
			'sort'			=> 5,
			'name'			=> 'index',
			'title'			=> 'モバイルトップページ',
			'description'	=> '',
			'contents'		=> '',
			'page_category_id'	=> '1',
			'status'			=> 1,
			'url'				=> '/mobile/index',
			'draft'				=> '',
			'author_id'			=> 1,
			'publish_begin'		=> '',
			'publish_end'		=> '',
			'exclude_search'	=> false,
			'code'				=> '',
			'unlinked_mobile'	=> false,
			'unlinked_smartphone'	=> false,
			'modified'			=> '',
			'created'			=> '2014-10-20'
		),
		array(
			'id'			=> 6,
			'sort'			=> 6,
			'name'			=> 'index',
			'title'			=> 'スマートフォントップページ',
			'description'	=> '',
			'contents'		=> '',
			'page_category_id'	=> '2',
			'status'			=> 1,
			'url'				=> '/smartphone/index',
			'draft'				=> '',
			'author_id'			=> 1,
			'publish_begin'		=> '',
			'publish_end'		=> '',
			'exclude_search'	=> false,
			'code'				=> '',
			'unlinked_mobile'	=> false,
			'unlinked_smartphone'	=> false,
			'modified'			=> '',
			'created'			=> '2014-10-20'
		),
		array(
			'id'			=> 7,
			'sort'			=> 7,
			'name'			=> 'recruit',
			'title'			=> 'スマートフォン採用情報',
			'description'	=> '',
			'contents'		=> '',
			'page_category_id'	=> '2',
			'status'			=> 1,
			'url'				=> '/smartphone/recruit',
			'draft'				=> '',
			'author_id'			=> 1,
			'publish_begin'		=> '',
			'publish_end'		=> '',
			'exclude_search'	=> false,
			'code'				=> '',
			'unlinked_mobile'	=> false,
			'unlinked_smartphone'	=> false,
			'modified'			=> '',
			'created'			=> '2014-10-20'
		),
		array(
			'id'			=> 8,
			'sort'			=> 8,
			'name'			=> 'service',
			'title'			=> 'モバイルサービス',
			'description'	=> '',
			'contents'		=> '',
			'page_category_id'	=> '1',
			'status'			=> 1,
			'url'				=> '/mobile/service',
			'draft'				=> '',
			'author_id'			=> 1,
			'publish_begin'		=> '',
			'publish_end'		=> '',
			'exclude_search'	=> false,
			'code'				=> '',
			'unlinked_mobile'	=> false,
			'unlinked_smartphone'	=> false,
			'modified'			=> '',
			'created'			=> '2014-10-20'
		)
	);
	
}
