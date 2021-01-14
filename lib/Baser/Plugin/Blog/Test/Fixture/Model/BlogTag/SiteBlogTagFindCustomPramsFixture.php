<?php

/**
 * Site Fixture
 */
class SiteBlogTagFindCustomPramsFixture extends CakeTestFixture
{

	/**
	 * Import
	 *
	 * @var array
	 */
	public $import = ['model' => 'Site'];

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => '2',
			'main_site_id' => '0',
			'name' => 'smartphone',
			'display_name' => 'スマートフォン',
			'title' => 'baserCMS inc.｜スマホ',
			'alias' => 's',
			'theme' => '',
			'status' => 1,
			'use_subdomain' => 0,
			'relate_main_site' => 0,
			'device' => 'smartphone',
			'lang' => '',
			'same_main_url' => 0,
			'auto_redirect' => 1,
			'auto_link' => 0,
			'domain_type' => '0',
			'created' => '2017-07-08 13:10:05',
			'modified' => '2017-07-09 19:44:02'
		],
	];

}
