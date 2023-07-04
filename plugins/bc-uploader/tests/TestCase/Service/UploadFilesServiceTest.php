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
        //正常系実行
        $param = [
            'conditions' => [
                'alt' => 'a'
            ],
            'uploader_category_id' => 1,
            'uploader_type' => 'img',
            'name' => 'a',
        ];
        $result = $this->execPrivateMethod($this->UploaderFilesService, 'createAdminIndexConditions', [$param]);
        $this->assertIsArray($result);
        $this->assertEquals('a', $result['alt']);
        $this->assertEquals(1, $result['UploaderFiles.uploader_category_id']);
        $this->assertEquals([
            ['UploaderFiles.name LIKE' => '%.png'],
            ['UploaderFiles.name LIKE' => '%.jpg'],
            ['UploaderFiles.name LIKE' => '%.gif']
        ], $result['or']);
        $this->assertEquals([
            'or' => [
                ['UploaderFiles.name LIKE' => '%a%'],
                ['UploaderFiles.alt LIKE' => '%a%'],
            ]
        ], $result['and']);
        //uploader_typeを変えるケース
        $param = [
            'uploader_type' => 'etc',
        ];
        $result = $this->execPrivateMethod($this->UploaderFilesService, 'createAdminIndexConditions', [$param]);
        $this->assertEquals([
            ['UploaderFiles.name NOT LIKE' => '%.png'],
            ['UploaderFiles.name NOT LIKE' => '%.jpg'],
            ['UploaderFiles.name NOT LIKE' => '%.gif']
        ], $result['and']);

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
        //準備
        //フィクチャーからデーターを生成: UploaderCategory
        UploaderFileFactory::make(['id' => 1, 'name' => 'social_new.jpg', 'atl' => 'social_new.jpg', 'uploader_category_id' => 1, 'user_id' => 1])->persist();
        UploaderFileFactory::make(['id' => 2, 'name' => 'widget-hero.jpg', 'atl' => 'widget-hero.jpg', 'uploader_category_id' => 1, 'user_id' => 1])->persist();
        //正常系実行
        $result = $this->UploaderFilesService->get(1);
        $this->assertEquals('social_new.jpg', $result->name);
        //異常系実行
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->UploaderFilesService->get(100);

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
