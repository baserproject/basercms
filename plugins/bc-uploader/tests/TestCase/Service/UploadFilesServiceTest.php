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
use BcUploader\Model\Table\UploaderFilesTable;
use BcUploader\Service\UploaderConfigsServiceInterface;
use BcUploader\Service\UploaderFilesService;
use BcUploader\Service\UploaderFilesServiceInterface;
use BcUploader\Test\Factory\UploaderFileFactory;

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
        //準備
        UploaderFileFactory::make(['id' => 1, 'name' => '2_a1.jpg', 'alt' => '2_1.jpg', 'user_id' => 1])->persist();
        UploaderFileFactory::make(['id' => 2, 'name' => '2_a2.png', 'alt' => '2_2.jpg', 'user_id' => 1])->persist();
        UploaderFileFactory::make(['id' => 3, 'name' => '2_3.txt', 'alt' => '2_3.txt', 'user_id' => 1])->persist();

        //正常系実行: パラメータなしで
        $result = $this->UploaderFilesService->getIndex([])->all();
        $this->assertCount(3, $result);
        //正常系実行: numパラメータを入れる
        $result = $this->UploaderFilesService->getIndex(['num' => 2])->all();
        $this->assertCount(2, $result);
        //正常系実行: nameパラメータを入れる
        $result = $this->UploaderFilesService->getIndex(['name' => 'a'])->all();
        $this->assertCount(2, $result);
        //正常系実行: uploader_typeパラメータを入れる
        $result = $this->UploaderFilesService->getIndex(['uploader_type' => 'img'])->all();
        $this->assertCount(2, $result);

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
