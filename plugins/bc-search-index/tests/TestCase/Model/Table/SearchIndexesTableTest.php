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

namespace BcSearchIndex\Test\TestCase\Model\Table;

use BcSearchIndex\Model\Table\SearchIndexesTable;
use BaserCore\TestSuite\BcTestCase;
use BcSearchIndex\Test\Scenario\Service\SearchIndexesServiceScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class SearchIndexesTableTest
 * @property SearchIndexesTable $SearchIndexes
 */
class SearchIndexesTableTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * @var SearchIndexesTable
     */
    public $SearchIndexes;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SearchIndexes = $this->getTableLocator()->get('BcSearchIndex.SearchIndexes');
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
        $this->loadFixtureScenario(SearchIndexesServiceScenario::class);
		$data['publish_begin'] = $publish_begin;
		$data['publish_end'] = $publish_end;
		$data['status'] = $status;
		$this->assertEquals($this->SearchIndexes->allowPublish($data), $expected);
	}

	public static function allowPublishDataProvider()
	{
		return [
			[null, null, false, false],
			[null, null, true, true],
			[null, date('Y-m-d H:i:s'), true, false],
			[null, date('Y-m-d H:i:s', strtotime("+1 hour")), true, true],
			[date('Y-m-d H:i:s'), null, true, true],
			[date('Y-m-d H:i:s', strtotime("+1 hour")), null, true, false],
			[date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), true, false]
		];
	}
}
