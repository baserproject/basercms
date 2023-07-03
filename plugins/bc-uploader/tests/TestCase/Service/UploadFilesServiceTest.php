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

use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use BcUploader\Model\Table\UploaderFilesTable;
use BcUploader\Service\UploaderConfigsServiceInterface;
use BcUploader\Service\UploaderFilesService;
use BcUploader\Service\UploaderFilesServiceInterface;
use BcUploader\Test\Factory\UploaderCategoryFactory;

/**
 * UploadFilesServiceTest
 * @property UploaderFilesService $UploaderFilesService
 */
class UploadFilesServiceTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UploaderFilesService = $this->getService(UploaderFilesServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test construct
     */
    public function test_construct()
    {
        $this->UploaderFilesService->__construct();
        $this->assertInstanceOf(UploaderFilesTable::class, $this->UploaderFilesService->UploaderFiles);
        $this->assertInstanceOf(UploaderConfigsServiceInterface::class, $this->UploaderFilesService->uploaderConfigsService);

    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {

    }

    /**
     * test createAdminIndexConditions
     */
    public function test_createAdminIndexConditions()
    {

    }

    /**
     * test getControlSource
     */
    public function test_getControlSource()
    {
        //準備
        //フィクチャーからデーターを生成: UploaderCategory
        UploaderCategoryFactory::make(['id' => 1, 'name' => 'blog'])->persist();
        UploaderCategoryFactory::make(['id' => 2, 'name' => 'contact'])->persist();
        UploaderCategoryFactory::make(['id' => 3, 'name' => 'service'])->persist();
        //フィクチャーからデーターを生成: User
        UserFactory::make(['id' => 1, 'name' => 'test user1', 'nickname' => 'Nghiem1'])->persist();
        UserFactory::make(['id' => 2, 'name' => 'test user2', 'nickname' => 'Nghiem2'])->persist();
        UserFactory::make(['id' => 3, 'name' => 'test user3', 'nickname' => 'Nghiem3'])->persist();

        //正常系実行: user_idパラメータを入れる
        $result = $this->UploaderFilesService->getControlSource('user_id');
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertEquals('Nghiem1', $result[1]);
        //正常系実行: uploader_category_idパラメータを入れる
        $result = $this->UploaderFilesService->getControlSource('uploader_category_id');
        $this->assertCount(3, $result);
        $this->assertEquals('contact', $result[2]);
        //正常系実行: パラメータなし
        $result = $this->UploaderFilesService->getControlSource();
        $this->assertFalse($result);

        //異常系実行
        $result = $this->UploaderFilesService->getControlSource('test');
        $this->assertFalse($result);

    }

    /**
     * test get
     */
    public function test_get()
    {

    }

    /**
     * test delete
     */
    public function test_delete()
    {

    }

    /**
     * test create
     */
    public function test_create()
    {

    }

    /**
     * test update
     */
    public function test_update()
    {

    }

    /**
     * test isEditable
     */
    public function test_isEditable()
    {

    }

    /**
     * test getNew
     */
    public function test_getNew()
    {

    }

}
