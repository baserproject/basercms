<?php

/**
 * SiteFixture
 */
class SiteFixture extends BaserTestFixture
{

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => '1',
			'main_site_id' => '0',
			'name' => 'mobile',
			'display_name' => 'ケータイ',
			'title' => 'baserCMS inc.｜ケータイ',
			'alias' => 'm',
			'theme' => '',
			'status' => 1,
			'device' => 'mobile',
			'lang' => '',
			'auto_redirect' => true,
			'auto_link' => true,
			'same_main_url' => false,
			'use_subdomain' => 0,
			'relate_main_site' => 0,
			'created' => '2016-08-01 21:20:15',
			'modified' => null
		],
		[
			'id' => '2',
			'main_site_id' => '0',
			'name' => 'smartphone',
			'display_name' => 'スマートフォン',
			'title' => 'baserCMS inc.｜スマホ',
			'alias' => 's',
			'theme' => '',
			'status' => 1,
			'device' => 'smartphone',
			'lang' => '',
			'auto_redirect' => true,
			'auto_link' => true,
			'same_main_url' => false,
			'use_subdomain' => 0,
			'relate_main_site' => 0,
			'created' => '2016-08-01 21:20:15',
			'modified' => null
		],
	];

}
