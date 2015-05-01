<?php
/**
 * PluginContentFixture
 *
 */
class PluginContentFixture extends BaserTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'content_id' => '1',
			'name' => 'news',
			'plugin' => 'blog',
			'created' => '2015-01-27 12:56:52',
			'modified' => null
		),
		array(
			'id' => '2',
			'content_id' => '1',
			'name' => 'contact',
			'plugin' => 'mail',
			'created' => '2015-01-27 12:56:52',
			'modified' => null
		),
	);

}
