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

namespace BcUploader\Test\TestCase\Service\Admin;

use BaserCore\TestSuite\BcTestCase;
use BcUploader\Service\Admin\UploaderFilesAdminService;
use BcUploader\Service\Admin\UploaderFilesAdminServiceInterface;
use BcUploader\Test\Factory\UploaderConfigFactory;
use BcUploader\Test\Factory\UploaderFileFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * UploadFilesAdminServiceTest
 */
class UploadFilesAdminServiceTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Test subject
     *
     * @var UploaderFilesAdminService
     */
    public $UploaderFilesAdminService;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UploaderFilesAdminService = $this->getService(UploaderFilesAdminServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        unset($this->UploaderFilesAdminService);
        parent::tearDown();
        $this->truncateTable('uploader_categories');
        $this->truncateTable('uploader_files');
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        UploaderConfigFactory::make(['name' => 'large_width', 'value' => 500])->persist();
        $rs = $this->UploaderFilesAdminService->getViewVarsForIndex(1);
        //戻る値を確認
        $this->assertEquals($rs['listId'], 1);
        $this->assertTrue($rs['isAjax']);
        $this->assertArrayHasKey('installMessage', $rs);
        $this->assertNotNull($rs['uploaderConfigs']);
    }

    /**
     * test getViewVarsForAjaxList
     */
    public function test_getViewVarsForAjaxList()
    {
        //データを生成
        UploaderConfigFactory::make(['name' => 'layout_type', 'value' => 'panel'])->persist();
        UploaderFileFactory::make(['name' => '2_3.jpg', 'atl' => '2_3.jpg', 'user_id' => 1])->persist();

        //対象メソッドをコール
        $rs = $this->UploaderFilesAdminService->getViewVarsForAjaxList(
            $this->UploaderFilesAdminService->getIndex([])->all(),
            1
        );

        //戻る値を確認
        $this->assertEquals(1, $rs['listId']);
        $this->assertEquals('panel', $rs['layoutType']);
        $this->assertCount(1, $rs['uploaderFiles']);
        $this->assertArrayHasKey('installMessage', $rs);
    }

    /**
     * test checkInstall
     */
    public function test_checkInstall()
    {
        //limitedフォルダーと.htaccessファイルが存在しない場合、
        $limitPath = '/var/www/html/webroot/files/uploads/limited';
        if (file_exists($limitPath . DS . '.htaccess'))
            unlink($limitPath . DS . '.htaccess');
        rmdir($limitPath);
        //対象メソッドをコール
        $rs = $this->execPrivateMethod($this->UploaderFilesAdminService, 'checkInstall', []);
        //戻る値を確認
        $this->assertEquals('', $rs);
        //.htaccessが生成されたか確認
        $this->assertTrue(file_exists($limitPath . DS . '.htaccess'));
        //.htaccessの中身を確認
        $this->assertEquals('Order allow,deny
Deny from all', file_get_contents($limitPath . DS . '.htaccess'));
    }

    /**
     * test getViewVarsForAjaxImage
     */
    public function test_getViewVarsForAjaxImage()
    {
        //データ生成
        UploaderFileFactory::make(['name' => 'test.jpg', 'atl' => '2_3.jpg'])->persist();
        //対象メソッドをコール
        $rs = $this->UploaderFilesAdminService->getViewVarsForAjaxImage('test.jpg', '1111');
        //戻る値を確認
        $this->assertEquals('1111', $rs['size']);
        $this->assertEquals('test.jpg', $rs['uploaderFile']->name);
    }
}
