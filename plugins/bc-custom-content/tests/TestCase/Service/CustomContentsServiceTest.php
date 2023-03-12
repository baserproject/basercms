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
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\CustomContentsService;
use BcCustomContent\Service\CustomContentsServiceInterface;
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
