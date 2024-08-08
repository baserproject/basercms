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
use BaserCore\Test\Factory\ContentFolderFactory;
use BaserCore\Test\Scenario\PermissionGroupsScenario;
use BaserCore\Test\Scenario\PluginsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Table\PermissionsTable as TablePermissionsTable;
use Cake\Event\Event;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

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
     * ScenarioAwareTrait
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
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $Permission = new TablePermissionsTable();

        $this->assertMatchesRegularExpression(
            // yyyy-MM-dd HH:mm:ssのパターン
            '{^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])\s([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$}',
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

    public static function getUrlPatternDataProvider()
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
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $Permission = new TablePermissionsTable();
        $max = $Permission->getMax('no', []);
        $this->assertEquals(1, $max);
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
        $this->loadFixtureScenario(PluginsScenario::class);
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

    /**
     * @dataProvider replaceTextDataProvider
     */
    public function test_replaceText($string, $expect)
    {
        $rs = $this->App->replaceText($string);
        $this->assertEquals($expect, $rs);
    }

    public static function replaceTextDataProvider()
    {
        return [
            ["\xE2\x85\xA0", "I"],
            ["\xE2\x85\xA1", "II"],
            ["\xE2\x85\xA2", "III"],
            ["\xE2\x85\xA3", "IV"],
            ["\xE2\x85\xA4", "V"],
            ["\xE2\x85\xA5", "VI"],
            ["\xE2\x85\xA6", "VII"],
            ["\xE2\x85\xA7", "VIII"],
            ["\xE2\x85\xA8", "IX"],
            ["\xE2\x85\xA9", "X"],
            ["\xE2\x85\xB0", "i"],
            ["\xE2\x85\xB1", "ii"],
            ["\xE2\x85\xB2", "iii"],
            ["\xE2\x85\xB3", "iv"],
            ["\xE2\x85\xB4", "v"],
            ["\xE2\x85\xB5", "vi"],
            ["\xE2\x85\xB6", "vii"],
            ["\xE2\x85\xB7", "viii"],
            ["\xE2\x85\xB8", "ix"],
            ["\xE2\x85\xB9", "x"],
            ["\xE2\x91\xA0", "(1)"],
            ["\xE2\x91\xA1", "(2)"],
            ["\xE2\x91\xA2", "(3)"],
            ["\xE2\x91\xA3", "(4)"],
            ["\xE2\x91\xA4", "(5)"],
            ["\xE2\x91\xA5", "(6)"],
            ["\xE2\x91\xA6", "(7)"],
            ["\xE2\x91\xA7", "(8)"],
            ["\xE2\x91\xA8", "(9)"],
            ["\xE2\x91\xA9", "(10)"],
            ["\xE2\x91\xAA", "(11)"],
            ["\xE2\x91\xAB", "(12)"],
            ["\xE2\x91\xAC", "(13)"],
            ["\xE2\x91\xAD", "(14)"],
            ["\xE2\x91\xAE", "(15)"],
            ["\xE2\x91\xAF", "(16)"],
            ["\xE2\x91\xB0", "(17)"],
            ["\xE2\x91\xB1", "(18)"],
            ["\xE2\x91\xB2", "(19)"],
            ["\xE2\x91\xB3", "(20)"],
            ["\xE3\x8A\xA4", "(上)"],
            ["\xE3\x8A\xA5", "(中)"],
            ["\xE3\x8A\xA6", "(下)"],
            ["\xE3\x8A\xA7", "(左)"],
            ["\xE3\x8A\xA8", "(右)"],
            ["\xE3\x8D\x89", "ミリ"],
            ["\xE3\x8D\x8D", "メートル"],
            ["\xE3\x8C\x94", "キロ"],
            ["\xE3\x8C\x98", "グラム"],
            ["\xE3\x8C\xA7", "トン"],
            ["\xE3\x8C\xA6", "ドル"],
            ["\xE3\x8D\x91", "リットル"],
            ["\xE3\x8C\xAB", "パーセント"],
            ["\xE3\x8C\xA2", "センチ"],
            ["\xE3\x8E\x9D", "cm"],
            ["\xE3\x8E\x8F", "kg"],
            ["\xE3\x8E\xA1", "m2"],
            ["\xE3\x8F\x8D", "K.K."],
            ["\xE2\x84\xA1", "TEL"],
            ["\xE2\x84\x96", "No."],
            ["\xE3\x8B\xBF", "令和"],
            ["\xE3\x8D\xBB", "平成"],
            ["\xE3\x8D\xBC", "昭和"],
            ["\xE3\x8D\xBD", "大正"],
            ["\xE3\x8D\xBE", "明治"],
            ["\xE3\x88\xB1", "(株)"],
            ["\xE3\x88\xB2", "(有)"],
            ["\xE3\x88\xB9", "(代)"],
           ];
    }

    /**
     * test beforeFind
     * @return void
     */
    public function testBeforeFind()
    {
        ContentFolderFactory::make(2)->persist();
        $this->entryEventToMock(self::EVENT_LAYER_MODEL, 'BaserCore.ContentFolders.beforeFind', function(Event $event) {
            $event->setData('options', ['limit' => 1]);
        });
        $contentFolders = $this->getTableLocator()->get('BaserCore.ContentFolders');
        $this->assertEquals(1, $contentFolders->find()->all()->count());
    }

    /**
     * test afterFind
     * @return void
     */
    public function testAfterFind()
    {
        ContentFolderFactory::make(2)->persist();
        $this->entryEventToMock(self::EVENT_LAYER_MODEL, 'BaserCore.ContentFolders.afterFind', function(Event $event) {
            $event->setData('result', $event->getData('result')->limit(1));
        });
        $contentFolders = $this->getTableLocator()->get('BaserCore.ContentFolders');
        $this->assertEquals(1, $contentFolders->find()->all()->count());
    }

    /**
     * testSortdown
     * @return void
     */
    public function testSortdown()
    {
        $this->loadFixtureScenario(PluginsScenario::class);
        $Plugins = $this->getTableLocator()->get('BaserCore.Plugins');
        $Plugins->sortdown(1, ['sortFieldName' => 'priority']);
        $this->assertEquals(2, $Plugins->get(1)->priority);
        $Plugins->sortdown(2, ['sortFieldName' => 'priority']);
        $this->assertEquals(2, $Plugins->get(2)->priority);
    }

}
