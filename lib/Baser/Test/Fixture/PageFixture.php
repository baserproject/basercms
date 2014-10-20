<?php
class PageFixture extends CakeTestFixture {

/**
* Import
*
* @var array
*/
	public $import = array( 
		'model'			=> 'Page',
		'connection'	=> 'baser'
	);
		
	public $records = array(
		array(
			'id'			=> 1,
			'sort'			=> 1,
			'name'			=> 'index',
			'title'			=> '',
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
			'title'			=> '',
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
			'title'			=> '',
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
			'name'			=> 'index',
			'title'			=> '',
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
			'id'			=> 5,
			'sort'			=> 5,
			'name'			=> 'index',
			'title'			=> '',
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
		)
	);
	
}