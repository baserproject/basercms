<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Test.Fixture.Default
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

class MailMessageFixture extends BaserTestFixture
{
	/**
	 * Name of the object
	 *
	 * @var string
	 */
	public $name = 'MailMessage';

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
	];

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
	];

}
