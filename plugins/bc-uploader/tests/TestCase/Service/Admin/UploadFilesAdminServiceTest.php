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
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BcUploader.Factory/UploaderConfigs',
    ];

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
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test getViewVarsForAjaxImage
     */
    public function test_getViewVarsForAjaxImage()
    {
        $this->markTestIncomplete('テストが未実装です');
    }
}
