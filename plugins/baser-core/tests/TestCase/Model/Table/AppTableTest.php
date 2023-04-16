<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\AppTable;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Table\PermissionsTable as TablePermissionsTable;

/**
 * Class AppTableTest
 */
class AppTableTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var AppTable
     */
    public $App;
        /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.Plugins',
    ];
    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->App = $this->getTableLocator()->get('BaserCore.App');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->App);
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $Permission = new TablePermissionsTable();

        $this->assertMatchesRegularExpression(
            // yyyy/MM/dd HH:mm:ssのパターン
            '{^[0-9]{4}/(0[1-9]|1[0-2])/(0[1-9]|[12][0-9]|3[01])\s([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$}',
            $Permission->find()->first()->created->__toString()
        );
    }

    /**
     * Test getUrlPattern
     *
     * @param string $url
     * @param array $expect
     * @return void
     * @dataProvider getUrlPatternDataProvider
     */
    public function testGetUrlPattern($url, $expect)
    {
        $this->assertEquals($expect, $this->App->getUrlPattern($url));
    }

    public function getUrlPatternDataProvider()
    {
        return [
            ['/news', ['/news']],
            ['/news/', ['/news/', '/news/index']],
            ['/news/index', ['/news/index', '/news/']],
            ['/news/archives/1', ['/news/archives/1']],
            ['/news/archives/index', ['/news/archives/index', '/news/archives/']]
        ];
    }

    /**
     * Test getMax
     *
     * @return void
     */
    public function testGetMax()
    {
        $Permission = new TablePermissionsTable();
        $max = $Permission->getMax('no', []);
        $this->assertEquals(23, $max);
    }

    /**
     * test offEvent And onEvent
     */
    public function testOffAndOnEvent()
    {
        $Permission = new TablePermissionsTable();
        $eventManager = $Permission->getEventManager();
        // 通常のイベント取得
        $listeners = $eventManager->listeners('Model.beforeSave');
        $this->assertEquals(2, count($listeners));
        // BcModelEventListener以外をオフ
        $Permission->offEvent('Model.beforeSave');
        $listeners = $eventManager->listeners('Model.beforeSave');
        $this->assertEquals(0, count($listeners));
        // BcModelEventListener以外をオン
        $Permission->onEvent('Model.beforeSave');
        $listeners = $eventManager->listeners('Model.beforeSave');
        $this->assertEquals(2, count($listeners));
    }

    /**
     * test changeSort
     */
    public function testChangeSort()
    {
        $Plugins = $this->getTableLocator()->get('BaserCore.Plugins');
        $Plugins->changeSort(1, 2, ['sortFieldName' => 'priority']);
        $this->assertEquals(3, $Plugins->get(1)->priority);
        $Plugins->changeSort(2, -1, ['sortFieldName' => 'priority']);
        $this->assertEquals(1, $Plugins->get(2)->priority);
    }

    /**
     * test getConditionAllowPublish
     */
    public function testGetConditionAllowPublish()
    {
        $conditions = $this->App->getConditionAllowPublish();
        $this->assertEquals(true, $conditions['App.status']);
        $this->assertArrayHasKey('App.publish_begin <=', $conditions[0]['or'][0]);
        $this->assertEquals(null, $conditions[0]['or'][1]['App.publish_begin IS']);
        $this->assertArrayHasKey('App.publish_end >=', $conditions[1]['or'][0]);
        $this->assertEquals(null, $conditions[1]['or'][1]['App.publish_end IS']);
    }

}
