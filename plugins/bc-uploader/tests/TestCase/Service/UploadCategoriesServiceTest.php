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
use BcUploader\Test\Factory\UploaderCategoryFactory;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
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
        $this->truncateTable('uploader_categories');
        $this->truncateTable('uploader_files');
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
        //データを生成
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        //対象メソッドをコール
        $rs = $this->UploaderCategoriesService->update(
            UploaderCategoryFactory::get(1),
            ['name' => '更新しました。']
        );
        //戻る値を確認
        $this->assertEquals('更新しました。', $rs->name);
        $this->assertEquals(1, $rs->id);
        //DBにデータが保存されたか確認すること
        $uploaderCategory = $this->UploaderCategoriesService->get(1);
        $this->assertEquals('更新しました。', $uploaderCategory->name);

        //異常系のテスト
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (name._empty: "カテゴリ名を入力してください。")');
        $this->UploaderCategoriesService->update(
            UploaderCategoryFactory::get(1),
            ['name' => '']
        );
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //データを生成
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        //対象メソッドをコール
        $rs = $this->UploaderCategoriesService->delete(1);
        //戻る値を確認
        $this->assertTrue($rs);
        //DBにデータが保存しないか確認すること
        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Record not found in table `uploader_categories`.');
        $this->UploaderCategoriesService->get(1);
    }

    /**
     * test copy
     */
    public function test_copy()
    {
        //データを生成
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        //対象メソッドをコール
        $rs = $this->UploaderCategoriesService->copy(1);
        //戻る値を確認
        $this->assertEquals('blog_copy', $rs->name);
        //DBにデータがコピーされたか確認すること
        $uploaderCategory = $this->UploaderCategoriesService->get(4);
        $this->assertEquals('blog_copy', $uploaderCategory->name);
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
