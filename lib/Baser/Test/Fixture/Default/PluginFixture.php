<?php

/**
 * PluginFixture
 */
class PluginFixture extends BaserTestFixture
{

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => '1',
			'name' => 'Blog',
			'title' => 'ブログ',
			'version' => '3.0.6.1',
			'status' => 1,
			'db_inited' => 1,
			'priority' => '1',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '2',
			'name' => 'Feed',
			'title' => 'フィードリーダー',
			'version' => '3.0.6.1',
			'status' => 1,
			'db_inited' => 1,
			'priority' => '2',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '3',
			'name' => 'Mail',
			'title' => 'メールフォーム',
			'version' => '3.0.6.1',
			'status' => 1,
			'db_inited' => 1,
			'priority' => '3',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		],
	];
}
