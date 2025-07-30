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
use BcCustomContent\Service\CustomLinksService;
use BcCustomContent\Service\CustomLinksServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Datasource\Exception\RecordNotFoundException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomLinksServiceTest
 */
class CustomLinksServiceTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomLinksService
     */
    public $CustomLinksService;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomLinksService = $this->getService(CustomLinksServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomLinksService);
        parent::tearDown();
    }

    /**
     * Test __construct
     */
    public function test__construct()
    {
        // テーブルがセットされている事を確認
        $this->assertEquals('CustomLinks', $this->CustomLinksService->CustomLinks->getAlias());
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //サービス
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_category',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //カスタムエントリテーブルでrecruit_categoryフィルドを生成
        $dataBaseService->addColumn('custom_entry_1_recruit_category', 'recruit_category', 'integer');
        //サービスメソッドを呼ぶ
        $result = $this->CustomLinksService->delete(1);
        //戻る値を確認
        $this->assertTrue($result);
        //custom_entry_1_recruit_categoryテーブルにrecruit_categoryが存在しないか確認すること
        $this->assertFalse($dataBaseService->columnExists('custom_entry_1_recruit_category', 'recruit_category'));
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_category');

        //存在しないカスタムリンクを削除
        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Record not found in table `custom_links`');
        $this->CustomLinksService->delete(1);
    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);
        //APIを呼ぶ
        $rs = $this->CustomLinksService->getIndex(1, ['status' => 'publish'])->toArray();
        //戻る値を確認
        $this->assertCount(2, $rs);
        $this->assertEquals('この仕事の特徴', $rs[0]['title']);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * test get
     */
    public function test_get()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);
        //サービスメソッドを呼ぶ
        $result = $this->CustomLinksService->get(1);
        //戻る値を確認
        $this->assertEquals('求人分類', $result->title);
        $this->assertArrayHasKey('custom_field', $result);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');

        //存在しないIDを指定した場合、
        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Record not found in table `custom_links`');
        $this->CustomLinksService->get(11);
    }

    /**
     * test updateFields
     */
    public function test_updateFields()
    {
        //サービス
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_category',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //カスタムエントリテーブルでfeatureフィルドを生成
        $dataBaseService->addColumn('custom_entry_1_recruit_category', 'feature', 'integer');
        //カスタムリンクがlft / rght を変更する。
        $data = $this->CustomLinksService->get(1);
        $data->lft = 1;
        $data->rght = 4;
        $this->CustomLinksService->update($data, $data->toArray());

        //対象メソッドを呼ぶ
        $this->CustomLinksService->updateFields(1, [$this->CustomLinksService->get(1)]);
        //custom_entry_1_recruit_categoryテーブルにfeatureが存在しないか確認すること
        $this->assertFalse($dataBaseService->columnExists('custom_entry_1_recruit_category', 'feature'));

        //lft / rght を最新にするかどうか確認すること
        $newLink = $this->CustomLinksService->get(1);
        $this->assertEquals(1, $newLink->lft);
        $this->assertEquals(4, $newLink->rght);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_category');
    }

    /**
     * test getGroupList
     */
    public function test_getGroupList()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //サービスメソッドを呼ぶ
        $rs = $this->CustomLinksService->getGroupList(1);
        //戻る値を確認
        $this->assertEquals('この仕事の特徴', $rs[2]);
    }

    /**
     * test create
     */
    public function test_create()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);

        //postDataを用意
        $data = [
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'lft' => 1,
            'rght' => 2,
            'name' => 'contact_column',
            'title' => 'お問い合わせ',
            'type' => 'text'
        ];
        //サービスメソッドを呼ぶ
        $result = $this->CustomLinksService->create($data);
        //戻る値を確認
        $this->assertEquals('contact_column', $result->name);
        $this->assertEquals('お問い合わせ', $result->title);
        //custom_entryテーブルにフィルドが生成されたか確認
        $this->assertTrue($dataBaseService->columnExists('custom_entry_1_contact', 'contact_column'));
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * test create
     */
    public function test_update()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //テストデータを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_category',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //カスタムエントリテーブルでrecruit_categoryフィルドを生成
        $dataBaseService->addColumn('custom_entry_1_recruit_category', 'recruit_category', 'integer');
        $data = [
            'title' => '求人分類_edit',
            'name' => 'recruit_category_edit',
            'type' => 'BcCcRelated_edit',
            'status' => 1,
            'default_value' => '新卒採用_edit'
        ];
        //APIを呼ぶ
        $result = $this->CustomLinksService->update($this->CustomLinksService->get(1), $data);
        //戻る値を確認
        $this->assertEquals('recruit_category_edit', $result->name);
        $this->assertEquals('求人分類_edit', $result->title);
        //custom_entry_1_recruit_categoryテーブルにrecruit_category_editが変更されたか確認すること
        $this->assertTrue($dataBaseService->columnExists('custom_entry_1_recruit_category', 'recruit_category_edit'));
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_category');

        //異常系のテスト
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (title._empty: "タイトルを入力してください。")');
        $this->CustomLinksService->update($this->CustomLinksService->get(1), ['title' => '']);
    }

    /**
     * test getControlSource
     */
    public function test_getControlSource()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //$field === 'parent_id'の場合、
        $rs = $this->CustomLinksService->getControlSource('parent_id', ['tableId' => 1]);
        //戻る値を確認
        $this->assertEquals('この仕事の特徴', $rs[2]);

        //$field !== 'parent_id'の場合、
        $rs = $this->CustomLinksService->getControlSource('test', ['tableId' => 1]);
        //戻る値を確認
        $this->assertEquals([], $rs);
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $result = $this->CustomLinksService->getList(1);
        $this->assertEquals('求人分類', $result[1]);
        $this->assertEquals('この仕事の特徴', $result[2]);
    }

    /**
     *test deleteFields
     */
    public function test_deleteFields()
    {
        //サービス
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_category',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //カスタムエントリテーブルでfeatureフィルドを生成
        //フィルドが削除される予定
        $dataBaseService->addColumn('custom_entry_1_recruit_category', 'feature', 'integer');
        // //カスタムエントリテーブルでrecruit_categoryフィルドを生成、フィルドが削除しない予定
        $dataBaseService->addColumn('custom_entry_1_recruit_category', 'recruit_category', 'integer');
        //対象メソッドを呼ぶ
        $this->CustomLinksService->deleteFields(1, [$this->CustomLinksService->get(1)]);
        //custom_entry_1_recruit_categoryテーブルにfeatureが存在しないか確認すること
        $this->assertFalse($dataBaseService->columnExists('custom_entry_1_recruit_category', 'feature'));
        //custom_entry_1_recruit_categoryテーブルにrecruit_category が存在するか確認すること
        $this->assertTrue($dataBaseService->columnExists('custom_entry_1_recruit_category', 'recruit_category'));
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_category');
    }

    /**
     * test findByName
     */
    public function testFindByName()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //サービスメソッドを呼ぶ
        $result = $this->CustomLinksService->findByName('feature', ['contain' => ['CustomFields']]);
        //戻る値を確認
        $this->assertEquals('この仕事の特徴', $result['title']);

        //存在しないフィールド名を指定した場合、空の配列を返す
        $result = $this->CustomLinksService->findByName('test', ['contain' => ['CustomFields']]);
        $this->assertEquals([], $result);
    }

    /**
     * test createIndexConditions
     */
    public function test_createIndexConditions()
    {
        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        $tableId = 1;

        // テストケース1: 基本的な条件のみ（status=null, name=null）
        $query1 = $this->CustomLinksService->CustomLinks->find();
        $params1 = [];
        $result1 = $this->CustomLinksService->createIndexConditions($query1, $tableId, $params1);

        // メソッドが正常に動作することを確認
        $this->assertInstanceOf(\Cake\Datasource\QueryInterface::class, $result1);

        // 実際にクエリを実行して結果を確認
        $results1 = $result1->toArray();
        $this->assertIsArray($results1);

        // テストケース2: status='publish'が指定された場合
        $query2 = $this->CustomLinksService->CustomLinks->find();
        $params2 = ['status' => 'publish'];
        $result2 = $this->CustomLinksService->createIndexConditions($query2, $tableId, $params2);

        // メソッドが正常に動作することを確認
        $this->assertInstanceOf(\Cake\Datasource\QueryInterface::class, $result2);

        // 実際にクエリを実行して結果を確認
        $results2 = $result2->toArray();
        $this->assertIsArray($results2);

        // テストケース3: nameパラメータが指定された場合（存在するname）
        $query3 = $this->CustomLinksService->CustomLinks->find();
        $params3 = ['name' => 'recruit_category'];
        $result3 = $this->CustomLinksService->createIndexConditions($query3, $tableId, $params3);

        // メソッドが正常に動作することを確認
        $this->assertInstanceOf(\Cake\Datasource\QueryInterface::class, $result3);

        // 実際にクエリを実行して結果を確認
        $results3 = $result3->toArray();
        $this->assertIsArray($results3);
        // recruit_categoryという名前のレコードが存在することを確認
        $this->assertGreaterThan(0, count($results3));

        // テストケース4: statusとnameの両方が指定された場合
        $query4 = $this->CustomLinksService->CustomLinks->find();
        $params4 = ['status' => 'publish', 'name' => 'recruit_category'];
        $result4 = $this->CustomLinksService->createIndexConditions($query4, $tableId, $params4);

        // メソッドが正常に動作することを確認
        $this->assertInstanceOf(\Cake\Datasource\QueryInterface::class, $result4);

        // 実際にクエリを実行して結果を確認
        $results4 = $result4->toArray();
        $this->assertIsArray($results4);

        // テストケース5: containパラメータが指定された場合
        $query5 = $this->CustomLinksService->CustomLinks->find();
        $params5 = ['contain' => ['CustomFields']];
        $result5 = $this->CustomLinksService->createIndexConditions($query5, $tableId, $params5);

        // メソッドが正常に動作することを確認
        $this->assertInstanceOf(\Cake\Datasource\QueryInterface::class, $result5);

        // テストケース6: containがnullの場合
        $query6 = $this->CustomLinksService->CustomLinks->find();
        $params6 = ['contain' => null];
        $result6 = $this->CustomLinksService->createIndexConditions($query6, $tableId, $params6);

        // メソッドが正常に動作することを確認
        $this->assertInstanceOf(\Cake\Datasource\QueryInterface::class, $result6);

        // クエリが正常に実行できることを確認
        $results6 = $result6->toArray();
        $this->assertIsArray($results6);

        // テストケース7: 存在しないnameを指定した場合
        $query7 = $this->CustomLinksService->CustomLinks->find();
        $params7 = ['name' => 'nonexistent_field'];
        $result7 = $this->CustomLinksService->createIndexConditions($query7, $tableId, $params7);

        // メソッドが正常に動作することを確認
        $this->assertInstanceOf(\Cake\Datasource\QueryInterface::class, $result7);

        // 結果が空であることを確認
        $results7 = $result7->toArray();
        $this->assertEmpty($results7);

        // テストケース8: 空のパラメータ配列
        $query8 = $this->CustomLinksService->CustomLinks->find();
        $params8 = [];
        $result8 = $this->CustomLinksService->createIndexConditions($query8, $tableId, $params8);

        // メソッドが正常に動作することを確認
        $this->assertInstanceOf(\Cake\Datasource\QueryInterface::class, $result8);

        // クエリが正常に実行できることを確認
        $results8 = $result8->toArray();
        $this->assertIsArray($results8);

        // テストケース9: status='publish'とcontainが両方指定された場合（マージのテスト）
        $query9 = $this->CustomLinksService->CustomLinks->find();
        $params9 = [
            'status' => 'publish',
            'contain' => ['CustomFields']
        ];
        $result9 = $this->CustomLinksService->createIndexConditions($query9, $tableId, $params9);

        // メソッドが正常に動作することを確認
        $this->assertInstanceOf(\Cake\Datasource\QueryInterface::class, $result9);

        // 実際にクエリを実行して結果を確認
        $results9 = $result9->toArray();
        $this->assertIsArray($results9);
    }
}
