<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.Test.Fixture.Default
 * @since			baserCMS v 4.1.6
 * @license			http://basercms.net/license/index.html
 */

class FeedDetailFixture extends BaserTestFixture {
	
/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'feed_config_id' => '1',
			'name' => 'baserCMSニュース',
			'url' => 'http://basercms.net/news/index.rss?site=http://localhost/',
			'category_filter' => '',
			'cache_time' => '+30 minutes',
			'created' => '2019-01-19 04:21:38',
			'modified' => '2019-01-19 04:21:38'
		),
	);

}
