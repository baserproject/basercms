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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\CustomContentsService;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Test\Factory\CustomContentFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
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
        'plugin.BcCustomContent.Factory/CustomContents',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcCustomContent.Factory/CustomTables',
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
        //データを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomTablesScenario::class);

        //$field = custom_table_id  コンテンツタイプのみ取得
        $result = $this->CustomContentsService->getControlSource('custom_table_id');
        $this->assertEquals('求人情報', $result[1]);

        //$field = list_order
        $options['custom_table_id'] = 1;
        $result = $this->CustomContentsService->getControlSource('list_order', $options);
        $listExpected = [
            'id' => 'No',
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
