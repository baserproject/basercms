<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */

namespace BcWidgetArea\Test\TestCase\Model\Table;

use BcWidgetArea\Model\Table\WidgetAreasTable;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class WidgetAreasTableTest
 * @property WidgetAreasTable $WidgetAreasTable
 */
class WidgetAreasTableTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->WidgetAreasTable = $this->getTableLocator()->get('BcWidgetArea.WidgetAreas');
    }
    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->WidgetAreasTable);
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertEquals('name', $this->WidgetAreasTable->getDisplayField());
        $this->assertEquals('id', $this->WidgetAreasTable->getPrimaryKey());
        $this->assertTrue($this->WidgetAreasTable->hasBehavior('Timestamp'));
    }

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

}
