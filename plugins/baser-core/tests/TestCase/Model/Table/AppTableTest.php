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
use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Table\PermissionsTable as TablePermissionsTable;
use Cake\Cache\Cache;

/**
 * Class AppTableTest
 * @package BaserCore\Test\TestCase\Model\Table
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
        $config = $this->getTableLocator()->exists('AppTable')? [] : ['className' => 'BaserCore\Model\Table\AppTable'];
        $this->App = $this->getTableLocator()->get('BaserCore.AppTable', $config);

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
     * Test deleteModelCache
     *
     * @return void
     */
    public function testDeleteModelCache()
    {
        $path = CACHE . 'models' . DS . 'dummy';

        if (touch($path)) {
            $this->App->deleteModelCache();
            $result = !file_exists($path);
            $this->assertTrue($result, 'Modelキャッシュを削除できません');
        } else {
            $this->markTestIncomplete('ダミーのキャッシュファイルの作成に失敗しました。');
        }
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
        $this->assertEquals(22, $max);
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
     * test getAppTableList
     */
    public function test_getAppTableList()
    {
        Cache::delete('appTableList', '_bc_env_');
        $result = $this->App->getAppTableList();
        $this->assertTrue(in_array('plugins', $result['BaserCore']));
        $this->assertTrue(in_array('plugins', Cache::read('appTableList', '_bc_env_')['BaserCore']));
    }

    public function test_writeCsv()
    {
        UserFactory::make(2)->persist();
        $result = $this->App->writeCsv('users', ['path' => TMP . 'users.csv']);
    }

}
