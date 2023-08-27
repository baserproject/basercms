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
use Cake\ORM\Exception\PersistenceFailedException;
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
        //データを生成
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        //対象メソッドをコール
        $rs = $this->UploaderCategoriesService->get(1);
        //戻る値を確認
        $this->assertEquals($rs->id, 1);
        $this->assertEquals($rs->name, 'blog');
    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        //データを生成
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        //対象メソッドをコール
        $rs = $this->UploaderCategoriesService->getIndex([])->toArray();
        //戻る値を確認
        $this->assertCount(3, $rs);
        $this->assertEquals('blog', $rs[0]->name);
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        //データを生成
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        //対象メソッドをコール
        $rs = $this->UploaderCategoriesService->getList();
        //戻る値を確認
        $this->assertCount(3, $rs);
        $this->assertEquals('blog', $rs[1]);
        $this->assertEquals('contact', $rs[2]);
        $this->assertEquals('service', $rs[3]);
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $this->assertEquals([], $this->UploaderCategoriesService->getNew()->toArray());
    }

    /**
     * test create
     */
    public function test_create()
    {
        //対象メソッドをコール
        $rs = $this->UploaderCategoriesService->create(['name' => 'blog']);
        //戻る値を確認
        $this->assertEquals('blog', $rs->name);
        //DBにデータが保存されたか確認すること
        $category = $this->UploaderCategoriesService->getIndex(['name' => 'blog'])->first();
        $this->assertEquals('blog', $category->name);

        //異常系のテスト
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (name._empty: "カテゴリ名を入力してください。")');
        $this->UploaderCategoriesService->create(['name' => '']);
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
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test getTitlesById
     */
    public function test_getTitlesById()
    {
        //データを生成
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        //対象メソッドをコール
        $rs = $this->UploaderCategoriesService->getTitlesById([1, 2]);
        //戻る値を確認
        $this->assertCount(2, $rs);
        $this->assertEquals('blog', $rs[1]);
        $this->assertEquals('contact', $rs[2]);
    }
}
