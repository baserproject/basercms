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
        //準備
        //フィクチャーからデーターを生成: UploaderCategory
        UploaderFileFactory::make(['id' => 1, 'name' => 'social_new.jpg', 'atl' => 'social_new.jpg', 'uploader_category_id' => 1, 'user_id' => 1])->persist();
        UploaderFileFactory::make(['id' => 2, 'name' => 'widget-hero.jpg', 'atl' => 'widget-hero.jpg', 'uploader_category_id' => 1, 'user_id' => 1])->persist();
        //正常系実行
        $this->assertTrue($this->UploaderFilesService->delete(1));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->UploaderFilesService->get(1);

    }

    /**
     * test delete not found
     */
    public function test_delete_not_found()
    {
        //準備
        //フィクチャーからデーターを生成: UploaderCategory
        UploaderFileFactory::make(['id' => 1, 'name' => 'social_new.jpg', 'atl' => 'social_new.jpg', 'uploader_category_id' => 1, 'user_id' => 1])->persist();
        UploaderFileFactory::make(['id' => 2, 'name' => 'widget-hero.jpg', 'atl' => 'widget-hero.jpg', 'uploader_category_id' => 1, 'user_id' => 1])->persist();
        //正常系実行
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->UploaderFilesService->delete(100);
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
