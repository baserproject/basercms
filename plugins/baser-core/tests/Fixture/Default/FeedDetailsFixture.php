<?php
declare(strict_types=1);

namespace BaserCore\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FeedDetailsFixture
 */
class FeedDetailsFixture extends TestFixture
{

    public $import = ['table' => 'feed_details'];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => '1',
            'feed_config_id' => '1',
            'name' => 'baserCMSニュース',
            'url' => 'https://basercms.net/news/index.rss',
            'category_filter' => 1,
            'cache_time' => '+30 minutes',
            'created' => '2015-01-27 12:57:59',
            'modified' => '2015-01-27 12:57:59'
        ],
    ];

}
