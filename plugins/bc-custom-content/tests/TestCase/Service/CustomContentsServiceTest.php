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
use BcCustomContent\Service\CustomContentsService;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomContentFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use BcCustomContent\Test\Scenario\CustomTablesScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomContentsServiceTest
 */
class CustomContentsServiceTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomContentsService
     */
    public $CustomContentsService;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomContentsService = $this->getService(CustomContentsServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomContentsService);
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertEquals('custom_contents', $this->CustomContentsService->CustomContents->getTable());
    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //テストメソッドを呼ぶ
        $result = $this->CustomContentsService->getIndex([])->toArray();
        //戻る値を確認
        $this->assertCount(3, $result);
        $this->assertEquals('サービステスト', $result[0]->description);
        $this->assertEquals('/recruit/', $result[1]->content->url);
    }

    /**
     * test get
     */
    public function test_get()
    {
        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //テストメソッドを呼ぶ
        $result = $this->CustomContentsService->get(1);
        //戻る値を確認
        $this->assertEquals('サービステスト', $result->description);
        $this->assertArrayHasKey('content', $result);
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        //テストメソッドを呼ぶ
        $result = $this->CustomContentsService->getNew();
        //戻る値を確認
        $this->assertEquals(10, $result->list_count);
        $this->assertEquals('id', $result->list_order);
        $this->assertEquals('DESC', $result->list_direction);
        $this->assertEquals('default', $result->template);
    }

    /**
     * test create
     */
    public function test_create()
    {
        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $postData = [
            'custom_table_id' => 1,
            'description' => 'test create',
            'template' => 'template_add',
            'content' => [
                'title' => 'test create',
                'site_id' => 1,
                'parent_id' => 0,
                'content' => 'add content'
            ]
        ];
        //正常ケースをテスト
        $result = $this->CustomContentsService->create($postData);
        //戻る値を確認
        $this->assertEquals(1, $result->custom_table_id);
        $this->assertEquals('test create', $result->content->title);
        $this->assertEquals(10, $result->list_count);

        //異常ケースをテスト
        $this->expectException('Cake\ORM\Exception\PersistenceFailedException');
        $this->expectExceptionMessage('Entity save failure. Found the following errors (content._required: "関連するコンテンツがありません"');
        $this->CustomContentsService->create([]);
    }

    /**
     * test create
     */
    public function test_update()
    {
        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //テストメソッドを呼ぶ
        $postData = [
            'custom_table_id' => 1,
            'list_count' => 10,
            'content' =>
                [
                    'title' => 'update title',
                    'site_id' => 1,
                ]
        ];
        $result = $this->CustomContentsService->update($this->CustomContentsService->get(1), $postData);
        //戻る値を確認
        $this->assertEquals($result->content->title, 'update title');

        //異常系をテスト
        $postData['content']['title'] = null;
        $this->expectException('Cake\ORM\Exception\PersistenceFailedException');
        $this->expectExceptionMessage('Entity save failure. Found the following errors (content.title._empty: "タイトルを入力してください。")');
        $this->CustomContentsService->update($this->CustomContentsService->get(1), $postData);
    }

    /**
     * test create
     */
    public function test_delete()
    {
        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //テストメソッドを呼ぶ
        $result = $this->CustomContentsService->delete(1);
        //戻る値を確認
        $this->assertTrue($result);

        //削除したコンテンツが存在するか確認
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->expectExceptionMessage('Record not found in table "custom_contents"');
        $this->CustomContentsService->get(1);
    }

    /**
     * test getControlSource
     */
    public function test_getControlSource()
    {
        //データを生成
        $this->loadFixtureScenario(InitAppScenario::class);

        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTablesService = $this->getService(CustomTablesServiceInterface::class);

        //テストデータを生成
        $customTablesService->create([
            'type' => 1,
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);

        //$field = custom_table_id  コンテンツタイプのみ取得
        $result = $this->CustomContentsService->getControlSource('custom_table_id');
        $this->assertEquals('お問い合わせタイトル', $result[1]);

        //$field = list_order
        $options['custom_table_id'] = 1;
        $result = $this->CustomContentsService->getControlSource('list_order', $options);
        $listExpected = [
            'id' => 'No',
            'published' => '公開日付',
            'created' => '登録日',
            'modified' => '編集日'
        ];
        $this->assertEquals($listExpected, $result);

        //$field = template
        $options['site_id'] = 1;
        $result = $this->CustomContentsService->getControlSource('template', $options);
        $this->assertEquals('default', $result['default']);

        //$field = test
        $result = $this->CustomContentsService->getControlSource('test');
        $this->assertEquals([], $result);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');

        //$field = list_order ＆ $optionsがない場合
        $this->expectException('BaserCore\Error\BcException');
        $this->expectExceptionMessage('list_order のコントロールソースを取得する場合は、custom_table_id の指定が必要です。');
        $this->CustomContentsService->getControlSource('list_order');

        //$field = template ＆ site_id
        $this->expectException('BaserCore\Error\BcException');
        $this->expectExceptionMessage('list_order のコントロールソースを取得する場合は、custom_table_id の指定が必要です。');
        $this->CustomContentsService->getControlSource('template');
    }

    /**
     * test getControlSource
     * $field = template ＆ site_idがない場合
     */
    public function test_getControlSource_template_Exception()
    {
        $this->expectException('BaserCore\Error\BcException');
        $this->expectExceptionMessage('template のコントロールソースを取得する場合は、site_id の指定が必要です。');
        $this->CustomContentsService->getControlSource('template');
    }

    /**
     * test getListOrders
     */
    public function test_getListOrders()
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
            'publish_begin' => '2021-10-01 00:00:00',
            'publish_end' => '9999-11-30 23:59:59',
            'published' => '2021-10-01 00:00:00',
            'has_child' => 0
        ]);

        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //対象メソッドをコール
        $rs = $this->CustomContentsService->getListOrders(1);
        $listExpect = [
            'published' => '公開日付',
            'created' => '登録日',
            'modified' => '編集日',
            'recruit_category' => '求人分類',
            'id' => 'No',
        ];
        //戻る値を確認
        $this->assertEquals($listExpect, $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getTemplates
     */
    public function test_getTemplates()
    {
        //データを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $rs = $this->CustomContentsService->getTemplates(1);
        $this->assertEquals('default', $rs['default']);
    }

    /**
     * test unsetTable
     */
    public function test_unsetTable()
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
            'publish_begin' => '2021-10-01 00:00:00',
            'publish_end' => '9999-11-30 23:59:59',
            'has_child' => 0
        ]);

        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);

        //対象メソッドをコール
        $this->CustomContentsService->unsetTable(1);

        //カスタムテーブルを除外したか確認すること
        $entities1 = CustomContentFactory::get(1);
        $this->assertNull($entities1->custom_table_id);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //テストメソッドを呼ぶ
        $result = $this->CustomContentsService->getList();
        //戻る値を確認
        $this->assertCount(3, $result);
        $this->assertEquals('サービスタイトル', $result[1]);
        $this->assertEquals('求人タイトル', $result[2]);
    }
}
