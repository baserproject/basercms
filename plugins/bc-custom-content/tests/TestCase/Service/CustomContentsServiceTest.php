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
use BcCustomContent\Service\CustomContentsService;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomContentFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
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
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BcCustomContent.Factory/CustomContents',
        'plugin.BaserCore.Factory/Contents',
    ];

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
        $this->assertCount(2, $result);
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test create
     */
    public function test_update()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getListOrders
     */
    public function test_getListOrders()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getTemplates
     */
    public function test_getTemplates()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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

        $entities2 = CustomContentFactory::get(2);
        $this->assertNull($entities2->custom_table_id);

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
        $this->assertCount(2, $result);
        $this->assertEquals('サービスタイトル',$result[1]);
        $this->assertEquals('求人タイトル',$result[2]);
    }
}
