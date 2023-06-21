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

namespace BcCustomContent\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Table\CustomEntriesTable;
use BcCustomContent\Model\Table\CustomTablesTable;
use BcCustomContent\Service\CustomEntriesService;
use BaserCore\Test\Scenario\InitAppScenario;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use BaserCore\Service\BcDatabaseService;
use BaserCore\Service\BcDatabaseServiceInterface;

/**
 * CustomEntriesServiceTest
 * @property CustomEntriesService $CustomEntriesService
 * @property BcDatabaseService $BcDatabaseService
 */
class CustomEntriesServiceTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BcCustomContent.Factory/CustomFields',
        'plugin.BcCustomContent.Factory/CustomLinks',
        'plugin.BcCustomContent.Factory/CustomTables',
        'plugin.BcCustomContent.Factory/CustomContents'
    ];

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomEntriesService = $this->getService(CustomEntriesServiceInterface::class);
        $this->BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test_construct()
    {
        $this->CustomEntriesService->__construct();
        $this->assertInstanceOf(CustomEntriesTable::class, $this->CustomEntriesService->CustomEntries);
        $this->assertInstanceOf(CustomTablesTable::class, $this->CustomEntriesService->CustomTables);
        $this->assertInstanceOf(BcDatabaseServiceInterface::class, $this->CustomEntriesService->BcDatabaseService);
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {

    }

    /**
     * test getFieldControlType
     */
    public function test_getFieldControlType()
    {

    }

    /**
     * test setup
     */
    public function test_setup()
    {

    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        //準備
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        $this->CustomEntriesService->setup(1);
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        //正常系実行
        $result = $this->CustomEntriesService->getIndex()->all();
        $this->assertCount(6, $result);
        //containパラメータを入れる
        $result = $this->CustomEntriesService->getIndex(['contain' => 'CustomTables'])->all();
        $this->assertCount(3, $result);
        //limitパラメータを入れる
        $result = $this->CustomEntriesService->getIndex(['limit' => 2])->all();
        $this->assertCount(2, $result);
        //ソートする
        $result = $this->CustomEntriesService->getIndex(['order' => 'name', 'direction' => 'desc'])->all()->toArray();
        $this->assertEquals('プログラマー 3', $result[0]->name);

    }

    /**
     * test createIndexConditions
     */
    public function test_createIndexConditions()
    {

    }

    /**
     * test getList
     */
    public function test_getList()
    {

    }

    /**
     * test createOrder
     */
    public function test_createOrder()
    {

    }

    /**
     * test get
     */
    public function test_get()
    {

    }

    /**
     * test createSelect
     */
    public function test_createSelect()
    {

    }

    /**
     * test create
     */
    public function test_create()
    {

    }

    /**
     * test update
     */
    public function test_update()
    {

    }

    /**
     * test delete
     */
    public function test_delete()
    {

    }

    /**
     * test addField
     */
    public function test_addField()
    {

    }

    /**
     * test renameField
     */
    public function test_renameField()
    {

    }

    /**
     * test removeField
     */
    public function test_removeField()
    {

    }

    /**
     * test createTable
     */
    public function test_createTable()
    {

    }

    /**
     * test renameTable
     */
    public function test_renameTable()
    {

    }

    /**
     * test dropTable
     */
    public function test_dropTable()
    {

    }

    /**
     * test addFields
     */
    public function test_addFields()
    {

    }

    /**
     * test getControlSource
     */
    public function test_getControlSource()
    {

    }

    /**
     * test getParentTargetList
     */
    public function test_getParentTargetList()
    {

    }

    /**
     * test isAllowPublish
     */
    public function test_isAllowPublish()
    {

    }

    /**
     * test getUrl
     */
    public function test_getUrl()
    {

    }

    /**
     * test autoConvert
     */
    public function test_autoConvert()
    {

    }

    /**
     * test moveUp
     */
    public function test_moveUp()
    {

    }

    /**
     * test moveDown
     */
    public function test_moveDown()
    {

    }

}
