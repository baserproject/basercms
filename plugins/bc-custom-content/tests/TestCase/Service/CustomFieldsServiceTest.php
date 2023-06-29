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

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\CustomFieldsServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomFieldsServiceTest
 */
class CustomFieldsServiceTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var $CustomFieldsService
     */
    public $CustomFieldsService;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BcCustomContent.Factory/CustomFields',
        'plugin.BcCustomContent.Factory/CustomLinks',
        'plugin.BcCustomContent.Factory/CustomTables',
    ];

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomFieldsService = $this->getService(CustomFieldsServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomFieldsService);
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->CustomFieldsService->CustomFields));
        $this->assertTrue(isset($this->CustomFieldsService->CustomEntries));
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        //テストメソッドを呼ぶ
        $result = $this->CustomFieldsService->getNew();
        //戻る値を確認
        $this->assertTrue($result->status);
        $this->assertEquals('', $result->placeholder);
        $this->assertEquals('BcCcText', $result->type);
        $this->assertEquals('', $result->source);
        $this->assertEquals('', $result->auto_convert);
    }

    /**
     * test get
     */
    public function test_get()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //対象メソッドをコール
        $rs = $this->CustomFieldsService->get(1);
        //戻る値を確認
        $this->assertEquals(1, $rs->id);
        $this->assertEquals('recruit_category', $rs->name);
        $this->assertEquals('求人分類', $rs->title);

        //存在しないIDを指定した場合、
        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Record not found in table "custom_fields"');
        $this->CustomFieldsService->get(111);
    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //対象メソッドをコール
        $rs = $this->CustomFieldsService->getIndex()->toArray();
        //戻る値を確認
        $this->assertCount(2, $rs);
        $this->assertEquals('求人分類', $rs[0]->title);
        $this->assertEquals('この仕事の特徴', $rs[1]->title);
    }

    /**
     * test create
     */
    public function test_create()
    {
        //Postデータを準備
        $data = [
            'title' => '求人分類',
            'name' => 'recruit_category',
            'type' => 'BcCcRelated',
            'status' => 1,
            'default_value' => '新卒採用',
        ];
        //正常系をテスト
        $rs = $this->CustomFieldsService->create($data);
        //戻る値を確認
        $this->assertEquals($rs->title, '求人分類');
        $this->assertEquals($rs->default_value, '新卒採用');

        //異常系をテスト
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (title._empty: "項目見出しを入力してください。")');
        $this->CustomFieldsService->create(['title' => null]);
    }

    /**
     * test update
     */
    public function test_update()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $customField = $this->CustomFieldsService->get(1);
        $customField->title = 'test edit title';
        //正常系をテスト
        $rs = $this->CustomFieldsService->update($customField, $customField->toArray());
        //戻る値を確認
        $this->assertEquals($rs->title, 'test edit title');

        //異常系をテスト
        $customField->title = null;
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (title._empty: "項目見出しを入力してください。")');
        $this->CustomFieldsService->update($customField, $customField->toArray());
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //サービスクラス
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTableService = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $customTableService->create([
            'id' => 1,
            'name' => 'recruit',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        $dataBaseService->addColumn('custom_entry_1_recruit', 'recruit_category', 'text');
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //対象メソッドをコール
        $rs = $this->CustomFieldsService->delete(1);
        //戻る値を確認
        $this->assertTrue($rs);
        //カラムrecruit_categoryが削除されたか確認すること
        $this->assertFalse($dataBaseService->columnExists('custom_entry_1_recruit', 'recruit_category'));
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit');
        //削除したカスタムフィールドが存在しないか確認すること
        $this->expectException(RecordNotFoundException::class);
        $this->CustomFieldsService->get(1);
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //対象メソッドをコール
        $rs = $this->CustomFieldsService->getList();
        //戻る値を確認
        $this->assertEquals('求人分類', $rs[1]);
        $this->assertEquals('この仕事の特徴', $rs[2]);
    }

    /**
     * test getFieldTypes
     */
    public function test_getFieldTypes()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getControlSource
     */
    public function test_getControlSource()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
