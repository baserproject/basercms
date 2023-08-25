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
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\CustomTablesService;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Factory\CustomContentFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomTablesServiceTest
 */
class CustomTablesServiceTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomTablesService
     */
    public $CustomTablesService;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomTablesService = $this->getService(CustomTablesServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomTablesService);
        parent::tearDown();
    }

    /**
     * Test __construct
     */
    public function test__construct()
    {
        // テーブルがセットされている事を確認
        $this->assertEquals('CustomTables', $this->CustomTablesService->CustomTables->getAlias());
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        //テスト対象メソッドをコール
        $rs = $this->CustomTablesService->getNew();
        //戻る値を確認
        $this->assertEquals(1, $rs->type);
        $this->assertEquals('title', $rs->display_field);
    }

    /**
     * test get
     */
    public function test_get()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);
        //テスト対象メソッドをコール
        $rs = $this->CustomTablesService->get(1);
        //戻る値を確認
        $this->assertEquals('contact', $rs->type);
        $this->assertEquals('contact', $rs->name);
        $this->assertEquals('お問い合わせタイトル', $rs->title);
        $this->assertEquals('お問い合わせ', $rs->display_field);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');

        //異常系をテスト
        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Record not found in table "custom_tables"');
        $this->CustomTablesService->get(111);
    }

    /**
     * test hasCustomContent
     */
    public function test_hasCustomContent()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        //テストデータを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_category',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        $customTable->create([
            'id' => 3,
            'name' => 'recruit_category_false',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        $this->loadFixtureScenario(CustomContentsScenario::class);

        //Trueを返すのユニットテスト
        $rs = $this->CustomTablesService->hasCustomContent(1);
        $this->assertTrue($rs);

        //Falseを返すのユニットテスト
        $rs = $this->CustomTablesService->hasCustomContent(3);
        $this->assertFalse($rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_category');
        $dataBaseService->dropTable('custom_entry_3_recruit_category_false');
    }

    /**
     * test getWithContentAndLinks
     */
    public function test_getWithContentAndLinks()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
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

        //対象メソッドをコール
        $rs = $this->CustomTablesService->getWithContentAndLinks(1);

        //戻る値を確認
        $this->assertEquals('recruit_categories', $rs->name);
        $this->assertEquals(1, $rs->custom_content->custom_table_id);
        $this->assertEquals(1, $rs->custom_content->content->entity_id);
        $this->assertEquals(1, $rs->custom_content->content->site->id);
        $this->assertCount(2, $rs->custom_links);
        $this->assertEquals('recruit_category', $rs->custom_links[0]->custom_field->name);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getWithLinks
     */
    public function test_getWithLinks()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //カスタムテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);

        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //対象メソッドをコール
        $rs = $this->CustomTablesService->getWithLinks(1);

        //戻る値を確認
        $this->assertEquals('recruit_categories', $rs->name);

        //カスタムリンクが存在するかどうか確認する事
        $this->assertCount(2, $rs->custom_links);
        $this->assertEquals('recruit_category', $rs->custom_links[0]->custom_field->name);

        //カスタムコンテンツが存在しないかどうか確認する事
        $this->assertArrayNotHasKey('custom_content', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //カスタムテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'test_1',
            'title' => '求人情報 1',
            'type' => '1',
            'display_field' => 'title 1',
            'has_child' => 0
        ]);
        $customTable->create([
            'id' => 2,
            'name' => 'test_2',
            'title' => '求人情報 2',
            'type' => '1',
            'display_field' => 'title 2',
            'has_child' => 0
        ]);

        //対象メソッドをコール
        $rs = $this->CustomTablesService->getIndex([])->toArray();
        //戻る値を確認
        $this->assertCount(2, $rs);
        $this->assertEquals('test_1', $rs[0]->name);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_test_1');
        $dataBaseService->dropTable('custom_entry_2_test_2');
    }

    /**
     * test create
     */
    public function test_create()
    {
        //テストデータを生成
        $data = [
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ];
        //対象メソッドをコール
        $rs = $this->CustomTablesService->create($data);
        //戻る値を確認
        $this->assertEquals('contact', $rs->name);

        //自動テーブルが生成できるか確認すること
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $this->assertTrue($dataBaseService->tableExists('custom_entry_1_contact'));

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');

        //エラーを発生した時のテスト
        //テストデータを生成
        $data = [
            'type' => 'contact',
            'name' => 'お問い合わせタイトル',
        ];
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (name.regex: "識別名は半角英数字とアンダースコアのみで入力してください。")');
        $this->CustomTablesService->create($data);
    }

    /**
     * test update
     */
    public function test_update()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);

        //テストデータを生成
        $data = [
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ];
        CustomFieldFactory::make([
            'id' => 1,
            'name' => 'recruit_category',
            'type' => 'text'
        ])->persist();
        $this->CustomTablesService->create($data);
        //アップデートメソッドを呼ぶ
        $rs = $this->CustomTablesService->update($this->CustomTablesService->get(1), ['name' => 'contact_edit']);
        //戻る値を確認
        $this->assertEquals('contact_edit', $rs->name);
        //テーブル名も変更されたの確認
        $this->assertTrue($dataBaseService->tableExists('custom_entry_1_contact_edit'));
        //変更した前テーブル名が存在しないの確認
        $this->assertFalse($dataBaseService->tableExists('custom_entry_1_contact'));

        //カスタムリンクを追加するテスト
        $postData = [
            'name' => 'contact_edit_2',
            'new' => [
                [
                    'no' => NULL,
                    'custom_table_id' => 1,
                    'custom_field_id' => 1,
                    'parent_id' => NULL,
                    'lft' => 1,
                    'rght' => 2,
                    'level' => 0,
                    'name' => 'add_new',
                    'title' => '求人分類',
                    'group_valid' => 0,
                ]
            ]
        ];
        //アップデートメソッドを呼ぶ
        $rs = $this->CustomTablesService->update($this->CustomTablesService->get(1), $postData);
        //戻る値を確認
        $this->assertEquals('contact_edit_2', $rs->name);
        //カスタムリンクが追加できるか確認する事
        $this->assertEquals('add_new', $rs->custom_links[0]->name);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact_edit_2');

        //エラーする時をテスト
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (name.regex: "識別名は半角英数字とアンダースコアのみで入力してください。")');
        $this->CustomTablesService->update($this->CustomTablesService->get(1), ['name' => 'あああああ']);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        //テストデータを生成
        $this->CustomTablesService->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //削除する前にカスタムテーブルをカスタムコンテンツに紐づいてきる
        $entities1 = CustomContentFactory::get(1);
        $this->assertNotNull($entities1->custom_table_id);

        //削除メソッドを呼ぶ
        $rs = $this->CustomTablesService->delete(1);
        //戻る値を確認
        $this->assertTrue($rs);
        //カスタムテーブルにレコードが削除できるか確認すること
        $this->assertCount(0, $this->CustomTablesService->getIndex([]));
        //テーブル名が存在しないの確認
        $this->assertFalse($dataBaseService->tableExists('custom_entry_1_contact'));

        //削除した後カスタムテーブルを除外したか確認すること
        $entities1 = CustomContentFactory::get(1);
        $this->assertNull($entities1->custom_table_id);

        //エラーを発生した時をテスト
        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Record not found in table "custom_tables"');
        $this->CustomTablesService->delete(1);
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);

        //テストデータを生成
        $this->CustomTablesService->create([
            'type' => 1,
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);
        $this->CustomTablesService->create([
            'type' => 2,
            'name' => 'recruit',
            'title' => '求人',
            'display_field' => '求人'
        ]);
        //カスタムテーブルのリストメソッドを呼ぶ
        $rs = $this->CustomTablesService->getList([]);
        //戻る値を確認
        $this->assertEquals($rs[1], 'お問い合わせタイトル');
        $this->assertEquals($rs[2], '求人');


        //type=1をテスト
        $rs = $this->CustomTablesService->getList(['type' => 1]);
        //戻る値は'type'＝１しかとれないのか確認すること
        $this->assertCount(1, $rs);
        $this->assertEquals($rs[1], 'お問い合わせタイトル');

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');
        $dataBaseService->dropTable('custom_entry_2_recruit');
    }

    /**
     * test getControlSource
     */
    public function test_getControlSource()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);

        //カスタムテーブルを生成
        $this->CustomTablesService->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);

        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //$field === 'display_field'の場合、
        $rs = $this->CustomTablesService->getControlSource('display_field', ['id' => 1]);
        //戻る値を確認
        $this->assertEquals($rs['title'], 'タイトル');
        $this->assertEquals($rs['name'], 'スラッグ');
        $this->assertEquals($rs['recruit_category'], '求人分類');
        $this->assertEquals($rs['feature'], 'この仕事の特徴');

        //$field !== 'display_field'の場合、
        $rs = $this->CustomTablesService->getControlSource('no', ['id' => 1]);
        //戻る値を確認
        $this->assertEquals($rs, []);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getCustomContentId
     */
    public function test_getCustomContentId()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        //カスタムテーブルを生成
        $this->CustomTablesService->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);

        //カスタムコンテンツIDが取得出来る場合。
        $rs = $this->CustomTablesService->getCustomContentId(1);
        //戻る値を確認
        $this->assertEquals(1, $rs);

        //カスタムコンテンツIDが取得できない場合、
        $rs = $this->CustomTablesService->getCustomContentId(10);
        //戻る値を確認
        $this->assertFalse($rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

}
