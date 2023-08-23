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

namespace BcUploader\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BcUploader\Service\UploaderCategoriesService;
use BcUploader\Service\UploaderCategoriesServiceInterface;
use BcUploader\Test\Scenario\UploaderCategoriesScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * UploadCategoriesServiceTest
 */
class UploadCategoriesServiceTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Test subject
     *
     * @var UploaderCategoriesService
     */
    public $UploaderCategoriesService;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UploaderCategoriesService = $this->getService(UploaderCategoriesServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        unset($this->UploaderCategoriesService);
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->UploaderCategoriesService->UploaderCategories));
    }

    /**
     * test get
     */
    public function test_get()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test create
     */
    public function test_create()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test update
     */
    public function test_update()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test copy
     */
    public function test_copy()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test batch
     */
    public function test_batch()
    {
        //データを生成
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        //対象メソッドをコール
        $rs = $this->UploaderCategoriesService->batch('delete', [1, 2, 3]);
        // 戻り値を確認
        $this->assertTrue($rs);
        // データが削除されていることを確認
        $blogTags = $this->UploaderCategoriesService->getIndex([])->toArray();
        $this->assertCount(0, $blogTags);

        // 存在しない id を指定された場合は例外が発生すること
        // サービスメソッドを呼ぶ
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->UploaderCategoriesService->batch('delete', [1, 2, 3]);
    }

    /**
     * test getTitlesById
     */
    public function test_getTitlesById()
    {
        $this->markTestIncomplete('テストが未実装です');
    }
}
