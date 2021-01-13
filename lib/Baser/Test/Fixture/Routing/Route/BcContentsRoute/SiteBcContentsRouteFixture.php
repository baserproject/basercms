<?php

/**
 * Site Fixture
 */
class SiteBcContentsRouteFixture extends BaserTestFixture
{

	/**
	 * Name of the object
	 *
	 * @var string
	 */
	public $name = 'Site';

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
			'use_subdomain' => 0,
			'relate_main_site' => 0,
			'device' => 'mobile',
			'lang' => '',
			'same_main_url' => 0,
			'auto_redirect' => 0,
			'auto_link' => 0,
			'domain_type' => 0,
			'created' => '2016-11-04 16:33:52',
			'modified' => '2016-11-04 16:39:36'
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
			'use_subdomain' => 0,
			'relate_main_site' => 1,
			'device' => 'smartphone',
			'lang' => '',
			'same_main_url' => 1,
			'auto_redirect' => 0,
			'auto_link' => 0,
			'domain_type' => 0,
			'created' => '2016-11-04 16:33:52',
			'modified' => '2016-11-04 16:42:25'
		],
		[
			'id' => '3',
			'main_site_id' => '0',
			'name' => 'english',
			'display_name' => 'English',
			'title' => 'baserCMS inc.｜English',
			'alias' => 'en',
			'theme' => '',
			'status' => 1,
			'use_subdomain' => 0,
			'relate_main_site' => 0,
			'device' => '',
			'lang' => '',
			'same_main_url' => 0,
			'auto_redirect' => 0,
			'auto_link' => 0,
			'domain_type' => 0,
			'created' => '2016-11-04 16:35:39',
			'modified' => '2016-11-04 16:40:34'
		],
		[
			'id' => '4',
			'main_site_id' => '0',
			'name' => 'subdomain',
			'display_name' => 'サブドメイン',
			'title' => 'baserCMS inc.｜サブドメイン',
			'alias' => 'sub',
			'theme' => '',
			'status' => 1,
			'use_subdomain' => 1,
			'relate_main_site' => 0,
			'device' => '',
			'lang' => '',
			'same_main_url' => 0,
			'auto_redirect' => 0,
			'auto_link' => 0,
			'domain_type' => 1,
			'created' => '2016-11-04 16:36:38',
			'modified' => '2016-11-04 16:41:14'
		],
		[
			'id' => '5',
			'main_site_id' => '0',
			'name' => 'another',
			'display_name' => '別ドメイン',
			'title' => 'baserCMS inc.｜別ドメイン',
			'alias' => 'another.com',
			'theme' => '',
			'status' => 1,
			'use_subdomain' => 1,
			'relate_main_site' => 0,
			'device' => '',
			'lang' => '',
			'same_main_url' => 0,
			'auto_redirect' => 0,
			'auto_link' => 0,
			'domain_type' => 2,
			'created' => '2016-11-04 16:38:36',
			'modified' => '2016-11-04 16:42:01'
		],
		[
			'id' => '6',
			'main_site_id' => '5',
			'name' => 'another_smartphone',
			'display_name' => '別ドメインスマートフォン',
			'title' => 'baserCMS inc.｜別ドメインスマートフォン',
			'alias' => 'another.com/s',
			'theme' => '',
			'status' => 1,
			'use_subdomain' => 1,
			'relate_main_site' => 0,
			'device' => 'smartphone',
			'lang' => '',
			'same_main_url' => 1,
			'auto_redirect' => 0,
			'auto_link' => 0,
			'domain_type' => 2,
			'created' => '2016-11-04 19:41:47',
			'modified' => '2016-11-04 19:41:47'
		],
	];

}
