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

use BaserCore\Model\Entity\Content;
use BaserCore\Model\Entity\User;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Table\CustomEntriesTable;
use BcCustomContent\Model\Table\CustomTablesTable;
use BcCustomContent\Service\CustomEntriesService;
use BaserCore\Test\Scenario\InitAppScenario;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomEntryFactory;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use Cake\Database\ValueBinder;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use BaserCore\Service\BcDatabaseService;
use BaserCore\Service\BcDatabaseServiceInterface;
use TypeError;

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
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test getFieldControlType
     */
    public function test_getFieldControlType()
    {
        Configure::write('BcCustomContent.fieldTypes.BcCcText.controlType', 'text');
        Configure::write('BcCustomContent.fieldTypes.BcCcCheckbox.controlType', 'checkbox');

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
        $this->assertCount(3, $result);
        //containパラメータを入れる
        $result = $this->CustomEntriesService->getIndex(['contain' => ['CustomTables' => ['CustomContents' => ['Contents']]]])->all();
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
        $this->assertStringContainsString('title LIKE', $whereSql);
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
        $order = 'test';
        $direction = 'asc';
        $result = $this->CustomEntriesService->createOrder($order, $direction);
        $this->assertEquals('CustomEntries.test asc, CustomEntries.id asc', $result);

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
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //正常系実行
        $result = $this->CustomEntriesService->delete(1);
        $this->assertTrue($result);
        // レコードが存在しない
        $rs = $this->CustomEntriesService->get(1);
        $this->assertNull($rs);
        //異常系実行
        $this->expectException(TypeError::class);
        $this->CustomEntriesService->delete(999);


    }

    /**
     * test addField
     */
    public function test_addField()
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
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //正常系実行
        $result = $this->CustomEntriesService->addField(1, 'nghiem', 'text');
        $this->assertTrue($result);
        $this->assertTrue($this->BcDatabaseService->columnExists('custom_entry_1_recruit_categories', 'nghiem'));
        //テストデータを削除
        $this->BcDatabaseService->removeColumn('custom_entry_1_recruit_categories', 'nghiem');
        //異常系実行
        $this->expectExceptionMessage('An invalid column type "text11" was specified for column "nghiem"');
        $this->CustomEntriesService->addField(1, 'nghiem', 'text11');

    }

    /**
     * test renameField
     */
    public function test_renameField()
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
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //正常系実行
        $result = $this->CustomEntriesService->renameField(1, 'name', 'name_test');
        $this->assertTrue($result);
        $this->assertFalse($this->BcDatabaseService->columnExists('custom_entry_1_recruit_categories', 'name'));
        $this->assertTrue($this->BcDatabaseService->columnExists('custom_entry_1_recruit_categories', 'name_test'));
        //旧カラム名を戻す
        $this->BcDatabaseService->renameColumn('custom_entry_1_recruit_categories', 'name_test', 'name');
        //異常系実行
        $this->expectExceptionMessage("The specified column doesn't exist: test");
        $result = $this->CustomEntriesService->renameField(1, 'test', 'test1');
    }

    /**
     * test removeField
     */
    public function test_removeField()
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
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //正常系実行
        $result = $this->CustomEntriesService->removeField(1, 'name');
        $this->assertTrue($result);
        $this->assertFalse($this->BcDatabaseService->columnExists('custom_entry_1_recruit_categories', 'name'));
        //異常系実行
        $this->expectExceptionMessage("The specified column doesn't exist: test");
        $result = $this->CustomEntriesService->renameField(1, 'test', 'test1');

    }

    /**
     * test createTable
     */
    public function test_createTable()
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
        //正常系実行
        $result = $this->CustomEntriesService->createTable(1);
        $this->assertTrue($result);
        $this->assertTrue($this->BcDatabaseService->tableExists('custom_entry_1_recruit_categories'));
        //異常系実行
        $this->expectException(RecordNotFoundException::class);
        $this->CustomEntriesService->createTable(99);
    }

    /**
     * test renameTable
     */
    public function test_renameTable()
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
        //正常系実行
        $result = $this->CustomEntriesService->renameTable(1, 'nghiem');
        $this->assertTrue($result);

    }

    /**
     * test dropTable
     */
    public function test_dropTable()
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
        //正常系実行
        $result = $this->CustomEntriesService->dropTable(1);
        $this->assertTrue($result);
        $this->assertFalse($this->BcDatabaseService->tableExists('custom_entry_1_recruit_categories'));
        //異常系実行
        $this->expectExceptionMessage('Record not found in table `custom_tables`');
        $this->CustomEntriesService->dropTable(99);

    }

    /**
     * test addFields
     */
    public function test_addFields()
    {
        //準備
       $customTable = $this->getService(CustomTablesServiceInterface::class);
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);

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
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //正常系実行
        $links = [
            new Entity([
                'custom_table_id' => 1,
                'custom_field_id' => 1,
                'lft' => 1,
                'rght' => 2,
                'name' => 'link1',
                'title' => '求人分類',
                'type' => 'text'
            ]),
            new Entity([
                'custom_table_id' => 1,
                'custom_field_id' => 2,
                'lft' => 1,
                'rght' => 2,
                'name' => 'link2',
                'title' => '求人分類2',
                'type' => 'text'
            ]),
        ];
        $this->CustomEntriesService->addFields(1, $links);
        $this->assertTrue($this->BcDatabaseService->columnExists('custom_entry_1_recruit_categories', 'link1'));
        $this->assertTrue($this->BcDatabaseService->columnExists('custom_entry_1_recruit_categories', 'link2'));
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');

        //異常系実行
        $this->expectExceptionMessage('Record not found in table `custom_tables`');
        $this->CustomEntriesService->addFields(99, $links);
    }

    /**
     * test getControlSource
     */
    public function test_getControlSource()
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
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        $this->CustomEntriesService->setup(1);
        UserFactory::make([
            'id' => 2,
            'name' => 'nghiem',
            'status' => 1,
            'method' => 'ALL',
            'real_name_1' => 'real_name_12',
            'real_name_2' => 'real_name_22',
            'nickname' => 'nickname2',
        ])->persist();
        UserFactory::make([
            'id' => 3,
            'name' => 'nghiem3',
            'status' => 0,
            'method' => 'ALL',
            'real_name_1' => 'real_name_13',
            'real_name_2' => 'real_name_23',
            'nickname' => 'nickname3',
        ])->persist();

        //正常系実行
        //空のパラメータを入れる
        $result = $this->CustomEntriesService->getControlSource('', []);
        $this->assertEquals([], $result);
        //creator_idのパラメータを入れる
        $options = ['status' => 1];
        $result = $this->CustomEntriesService->getControlSource('creator_id', $options);
        $this->assertCount(2, $result);
        $this->assertEquals('nickname2', $result[2]);

    }

    /**
     * test getParentTargetList
     */
    public function test_getParentTargetList()
    {
        $this->markTestIncomplete('このテストは未確認。');
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
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        $this->CustomEntriesService->setup(1);
        //正常系実行
        $result = $this->CustomEntriesService->getParentTargetList(1);
        $this->assertCount(1, $result);
        $this->assertEquals('title1', $result[1]);

    }

    /**
     * test isAllowPublish
     */
    public function test_isAllowPublish()
    {
        //正常系実行:status = false 開始日と終了日とを指定しない
        $entity = new Entity([
            'status' => false,
            'publish_begin' => '',
            'publish_end' => '',
        ]);
        $this->assertFalse($this->CustomEntriesService->isAllowPublish($entity));

        //正常系実行:status = true 開始日と終了日とを指定しない
        $entity = new Entity([
            'status' => true,
            'publish_begin' => '',
            'publish_end' => '',
        ]);
        $this->assertTrue($this->CustomEntriesService->isAllowPublish($entity));

        //正常系実行: 将来の開始日を指定する
        $entity = new Entity([
            'status' => true,
            'publish_begin' => \Cake\I18n\DateTime::tomorrow(),
            'publish_end' => '',
        ]);
        $this->assertFalse($this->CustomEntriesService->isAllowPublish($entity));

        //正常系実行: 過去の開始日を指定する
        $entity = new Entity([
            'status' => true,
            'publish_begin' => '',
            'publish_end' => \Cake\I18n\DateTime::yesterday(),
        ]);
        $this->assertFalse($this->CustomEntriesService->isAllowPublish($entity));

    }

    /**
     * test getUrl
     */
    public function test_getUrl()
    {
        //準備
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $entity = new Entity([
            'id' => '1',
            'name' => 'Nghiem'
        ]);
        $content = new Content([
            'id' => 1,
            'url' => 'test/',
            'name' => '',
            'plugin' => 'BcCustomContent',
            'type' => 'CustomContent',
            'site_id' => 1,
            'parent_id' => null,
            'title' => 'サービスタイトル',
            'lft' => 1,
            'rght' => 2,
            'entity_id' => 1,
            'status' => true
        ]);
        //正常系実行
        $result = $this->CustomEntriesService->getUrl($content, $entity);
        $this->assertEquals('https://localhost/test/view/Nghiem', $result);
        $result = $this->CustomEntriesService->getUrl($content, $entity, false);
        $this->assertEquals('test/view/Nghiem', $result);
        //name = ''のケース
        $entity = new Entity([
            'id' => '1',
            'name' => ''
        ]);
        $result = $this->CustomEntriesService->getUrl($content, $entity);
        $this->assertEquals('https://localhost/test/view/1', $result);
        $result = $this->CustomEntriesService->getUrl($content, $entity, false);
        $this->assertEquals('test/view/1', $result);

    }

    /**
     * test autoConvert
     */
    public function test_autoConvert()
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
        //リンクとフィールドでーたを生成
        CustomFieldFactory::make([
            'id' => 1,
            'title' => '求人分類',
            'name' => 'recruit_category',
            'type' => 'BcCcRelated',
            'auto_convert' => 'CONVERT_HANKAKU',
        ])->persist();
        CustomLinkFactory::make([
            'id' => 1,
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'lft' => 1,
            'rght' => 2,
            'level' => 0,
            'name' => 'link1',
            'title' => '求人分類',
            'status' => 1,
        ])->persist();
        CustomFieldFactory::make([
            'id' => 2,
            'title' => 'この仕事の特徴',
            'name' => 'feature',
            'type' => 'BcCcMultiple',
            'status' => 1,
            'auto_convert' => 'CONVERT_ZENKAKU',
        ])->persist();
        CustomLinkFactory::make([
            'id' => 2,
            'custom_table_id' => 1,
            'custom_field_id' => 2,
            'lft' => 1,
            'rght' => 2,
            'level' => 0,
            'name' => 'link2',
            'title' => 'この仕事の特徴',
            'status' => 1,
        ])->persist();
        $CustomEntries->setLinks(1);
        //正常系実行
        $data = [
            'link1' => 'Ｎｇｈｉｅｍ',
            'link2' => 'Nghiem',
        ];
        $result = $this->CustomEntriesService->autoConvert($data);
        $dataExpect = [
            'link1' => 'Nghiem',
            'link2' => 'Ｎｇｈｉｅｍ',
        ];
        $this->assertEquals($dataExpect, $result);

    }

    /**
     * test moveUp
     */
    public function test_moveUp()
    {
        $this->markTestIncomplete('このテストは未確認。');
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
        $result = $this->CustomEntriesService->moveUp(3);
        $this->assertTrue($result);
        //異常系実行
        $this->expectException(RecordNotFoundException::class);
        $this->CustomEntriesService->moveUp(99);

    }

    /**
     * test moveDown
     */
    public function test_moveDown()
    {
        $this->markTestIncomplete('このテストは未確認。');
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
        $result = $this->CustomEntriesService->moveDown(2);
        $this->assertTrue($result);
        //異常系実行
        $this->expectException(RecordNotFoundException::class);
        $this->CustomEntriesService->moveDown(99);

    }

}
