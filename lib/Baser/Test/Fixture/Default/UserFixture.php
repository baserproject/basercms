<?php

/**
 * UserFixture
 */
class UserFixture extends BaserTestFixture
{

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => '1',
			'name' => 'basertest',
			'password' => '32b5225591a89829bd165f2883a013bb6764c7d6',
			'real_name_1' => 'basertest',
			'real_name_2' => null,
			'email' => 'basertest@example.com',
			'user_group_id' => '1',
			'nickname' => null,
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '2',
			'name' => 'basertest2',
			'password' => '47281835ff5f903924455e855517cda7ae8d4523',
			'real_name_1' => 'basertest2',
			'real_name_2' => null,
			'email' => 'basertest2@example.com',
			'user_group_id' => '2',
			'nickname' => null,
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		],
	];

}
