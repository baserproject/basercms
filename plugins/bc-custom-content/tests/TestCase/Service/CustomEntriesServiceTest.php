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
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use Cake\Database\ValueBinder;
use Cake\ORM\TableRegistry;
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
        //正常系実行
        $result = $this->CustomEntriesService->getFieldControlType('BcCcText');
        $this->assertEquals('text', $result);
        $result = $this->CustomEntriesService->getFieldControlType('BcCcCheckbox');
        $this->assertEquals('checkbox', $result);
        //異常系実行
        $result = $this->CustomEntriesService->getFieldControlType('a');
        $this->assertEquals('', $result);
    }

    /**
     * test setup
     */
    public function test_setup()
    {
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
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        //正常系実行
        $this->CustomEntriesService->setup(1);
        $this->assertTrue($this->BcDatabaseService->tableExists('custom_entry_1_recruit_categories'));
        $this->CustomEntriesService->dropTable(1);
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
        //準備
        $CustomEntries = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
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
        $query = $CustomEntries->find();
        $params = [
            'title' => null,
            'creator_id' => null,
            'status' => null,
        ];
        $result = $this->CustomEntriesService->createIndexConditions($query, $params);
        $this->assertEquals($query, $result);
        $params = [
            'title' => 'a',
            'creator_id' => 1,
            'status' => 'publish',
        ];
        $result = $this->CustomEntriesService->createIndexConditions($query, $params);
        $whereSql = $result->clause('where')->sql(new ValueBinder());
        $this->assertStringContainsString('title like', $whereSql);
        $this->assertStringContainsString('CustomEntries.status =', $whereSql);
        $this->assertStringContainsString('CustomEntries.publish_begin <=', $whereSql);
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        //準備
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'name',
            'has_child' => 0
        ]);
        $this->CustomEntriesService->setup(1);
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        //正常系実行
        $result = $this->CustomEntriesService->getList();
        $this->assertCount(3, $result);
        //nameパラメータを入れる
        $result = $this->CustomEntriesService->getList(['conditions' => ['name' => 'プログラマー 2']]);
        $this->assertCount(1, $result);
        $this->assertEquals('プログラマー 2', $result[2]);

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

        //idで取得
        $result = $this->CustomEntriesService->get(1);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('プログラマー', $result->name);
        //名前で取得
        $result = $this->CustomEntriesService->get('プログラマー 2');
        $this->assertEquals(2, $result->id);

        //異常系実行
        $result = $this->CustomEntriesService->get(99);
        $this->assertNull($result);

    }

    /**
     * test createSelect
     */
    public function test_createSelect()
    {
        //準備
        $CustomEntries = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
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
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //正常系実行: use_api = null
        $options = [
            'use_api' => null
        ];
        $result = $this->CustomEntriesService->createSelect($options);
        $this->assertCount(15, $result);
        $this->assertEquals('CustomEntries.id', $result[0]);
        $this->assertEquals('CustomEntries.created', $result[14]);
        //正常系実行: use_api = 1
        $options = [
            'use_api' => 1
        ];
        CustomLinkFactory::make([
            'id' => 99,
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'lft' => 1,
            'rght' => 2,
            'level' => 2,
            'name' => 'Nghiem',
            'title' => '求人分類',
            'display_admin_list' => 1,
            'use_api' => 1,
            'status' => 1,
        ])->persist();
        $CustomEntries->setLinks(1);
        $result = $this->CustomEntriesService->createSelect($options);
        $this->assertCount(18, $result);
        $this->assertEquals('CustomEntries.recruit_category', $result[15]);
        $this->assertEquals('CustomEntries.feature', $result[16]);
        $this->assertEquals('CustomEntries.Nghiem', $result[17]);
    }

    /**
     * test create
     */
    public function test_create()
    {
        //準備
        $CustomEntries = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
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
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $CustomEntries->setLinks(1);

        //正常系実行
        $postData = [
            'id' => 99,
            'custom_table_id' => 1,
            'title' => 'title99',
            'creator_id' => 1,
            'status' => 1,
        ];
        $result = $this->CustomEntriesService->create($postData);
        $this->assertEquals(99, $result->id);
        $this->assertEquals('title99', $result->title);
        //異常系実行
        $postData = [
            'custom_table_id' => 1,
            'creator_id' => 1,
            'status' => 1,
        ];
        $this->expectExceptionMessage('Entity save failure. Found the following errors (title._required: "This field is required")');
        $this->CustomEntriesService->create($postData);

    }

    /**
     * test update
     */
    public function test_update()
    {
        //準備
        $CustomEntries = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
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
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $CustomEntries->setLinks(1);

        //正常系実行
        $postData = [
            'title' => 'Nghiem',
        ];
        $customEntry = $this->CustomEntriesService->get(1);
        $result = $this->CustomEntriesService->update($customEntry, $postData);
        $this->assertEquals('Nghiem', $result->title);
        //異常系実行
        $postData = [
            'title' => '',
        ];
        $this->expectExceptionMessage('Entity save failure. Found the following errors (title._empty: "タイトルは必須項目です。").');
        $this->CustomEntriesService->update($customEntry, $postData);

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
