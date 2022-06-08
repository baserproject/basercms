<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\SearchIndexesTable;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class SearchIndexesTableTest
 * @package BaserCore\Test\TestCase\Model\Table
 * @property SearchIndexesTable $SearchIndexes
 */
class SearchIndexesTableTest extends BcTestCase
{

    /**
     * @var SearchIndexesTable
     */
    public $SearchIndexes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.SearchIndexes',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SearchIndexes = $this->getTableLocator()->get('BaserCore.SearchIndexes');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SearchIndexes);
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertTrue($this->SearchIndexes->hasBehavior('Timestamp'));
    }

	/**
	 * 公開状態を取得する
	 *
	 * @dataProvider allowPublishDataProvider
	 */
	public function testAllowPublish($publish_begin, $publish_end, $status, $expected)
	{
		$data['publish_begin'] = $publish_begin;
		$data['publish_end'] = $publish_end;
		$data['status'] = $status;
		$this->assertEquals($this->SearchIndexes->allowPublish($data), $expected);
	}

	public function allowPublishDataProvider()
	{
		return [
			['0000-00-00 00:00:00', '0000-00-00 00:00:00', false, false],
			['0000-00-00 00:00:00', '0000-00-00 00:00:00', true, true],
			['0000-00-00 00:00:00', date('Y-m-d H:i:s'), true, false],
			['0000-00-00 00:00:00', date('Y-m-d H:i:s', strtotime("+1 hour")), true, true],
			[date('Y-m-d H:i:s'), '0000-00-00 00:00:00', true, true],
			[date('Y-m-d H:i:s', strtotime("+1 hour")), '0000-00-00 00:00:00', true, false],
			[date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), true, false]
		];
	}
}
