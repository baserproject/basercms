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

namespace BcThemeFile\Test\TestCase\Service\Admin;

use BaserCore\TestSuite\BcTestCase;
use BcThemeFile\Service\Admin\ThemeFilesAdminService;
use BcThemeFile\Service\Admin\ThemeFilesAdminServiceInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * ThemeFilesAdminServiceTest
 */
class ThemeFilesAdminServiceTest extends BcTestCase
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
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
    ];

    /**
     * Test subject
     *
     * @var ThemeFilesAdminService
     */
    public $ThemeFilesAdminService;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemeFilesAdminService = $this->getService(ThemeFilesAdminServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        unset($this->ThemeFilesAdminService);
        parent::tearDown();
    }

    /**
     * test construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->ThemeFilesAdminService->ThemeFoldersService));
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        //テスト前の準備
        $param = [
            'fullpath' => '/var/www/html/plugins/bc-front/templates',
            'path' => '/var/www/html/plugins/bc-front/templates',
            'plugin' => 'bc-front',
            'theme' => 'bc-front',
            'type' => 'folder',
        ];
        //対象メソッドをコール
        $rs = $this->ThemeFilesAdminService->getViewVarsForIndex($param);
        //戻る値を確認
        $this->assertCount(12, $rs['themeFiles']);
        $this->assertNotNull($rs['currentPath']);
        $this->assertNotNull($rs['path']);
        $this->assertNotNull($rs['plugin']);
        $this->assertNotNull($rs['theme']);
        $this->assertNotNull($rs['type']);
        $this->assertEquals($rs['pageTitle'], 'bc-front：bc-front');
    }

    /**
     * test getViewVarsForAdd
     */
    public function test_getViewVarsForAdd()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test getViewVarsForView
     */
    public function test_getViewVarsForView()
    {
        $this->markTestIncomplete('テストが未実装です');
    }
}
