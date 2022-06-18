<?php

/**
 * SiteSubDomainFixture
 */
class SiteSubDomainFixture extends BaserTestFixture
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
			'name' => 'subdomain',
			'display_name' => 'subdomain',
			'title' => 'subdomain',
			'alias' => 'subdomain',
			'theme' => '',
			'status' => 1,
			'device' => '',
			'lang' => '',
			'auto_redirect' => true,
			'auto_link' => true,
			'same_main_url' => false,
			'use_subdomain' => 1,
			'relate_main_site' => 0,
			'created' => '2022-06-18 21:20:15',
			'modified' => null
		],
	];

}
