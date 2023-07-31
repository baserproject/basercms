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
use BcThemeFile\Service\Admin\ThemeFoldersAdminService;
use BcThemeFile\Service\Admin\ThemeFoldersAdminServiceInterface;

/**
 * ThemeFoldersAdminServiceTest
 */
class ThemeFoldersAdminServiceTest extends BcTestCase
{
    /**
     * Test subject
     *
     * @var ThemeFoldersAdminService
     */
    public $ThemeFoldersAdminService;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemeFoldersAdminService = $this->getService(ThemeFoldersAdminServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        unset($this->ThemeFoldersAdminService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        $path = '/var/www/html/plugins/bc-front/templates';
        //テスト前の準備
        $param = [
            'fullpath' => '/var/www/html/plugins/bc-front/templates',
            'path' => '/var/www/html/plugins/bc-front/templates',
            'plugin' => 'bc-front',
            'theme' => 'bc-front',
            'type' => 'folder',
        ];
        //対象メソッドをコール
        $rs = $this->ThemeFoldersAdminService->getViewVarsForEdit(
            $this->ThemeFoldersAdminService->get($path),
            $this->ThemeFoldersAdminService->getForm([]),
            $param
        );
        $this->assertEquals(1, 1);
        //戻る値を確認
        $this->assertArrayHasKey('themeFolderForm', $rs);
        $this->assertArrayHasKey('themeFolder', $rs);
        $this->assertNotNull($rs['currentPath']);
        $this->assertNotNull($rs['theme']);
        $this->assertNotNull($rs['plugin']);
        $this->assertNotNull($rs['type']);
        $this->assertNotNull($rs['path']);
        $this->assertTrue($rs['isWritable']);
        $this->assertEquals($rs['pageTitle'], 'Bc-front｜フォルダ編集');
    }

    /**
     * test getViewVarsForAdd
     */
    public function test_getViewVarsForAdd()
    {
        $path = '/var/www/html/plugins/bc-front/templates';
        //テスト前の準備
        $param = [
            'fullpath' => '/var/www/html/plugins/bc-front/templates',
            'path' => '/var/www/html/plugins/bc-front/templates',
            'plugin' => 'bc-front',
            'theme' => 'bc-front',
            'type' => 'folder',
        ];
        //対象メソッドをコール
        $rs = $this->ThemeFoldersAdminService->getViewVarsForAdd(
            $this->ThemeFoldersAdminService->get($path),
            $this->ThemeFoldersAdminService->getForm([]),
            $param
        );
        //戻る値を確認
        $this->assertArrayHasKey('themeFolderForm', $rs);
        $this->assertArrayHasKey('themeFolder', $rs);
        $this->assertNotNull($rs['currentPath']);
        $this->assertNotNull($rs['theme']);
        $this->assertNotNull($rs['plugin']);
        $this->assertNotNull($rs['type']);
        $this->assertNotNull($rs['path']);
        $this->assertTrue($rs['isWritable']);
        $this->assertEquals($rs['pageTitle'], 'Bc-front｜フォルダ作成');
    }

    /**
     * test isWritableDir
     */
    public function test_isWritableDir()
    {
        //trueを返す場合、
        $fullPath = '/var/www/html/plugins/bc-front/templates';
        $this->assertTrue($this->ThemeFoldersAdminService->isWritableDir($fullPath));

        //falseを返す場合、
        $fullPath = 'test';
        $this->assertFalse($this->ThemeFoldersAdminService->isWritableDir($fullPath));
    }

    /**
     * test getViewVarsForView
     */
    public function test_getViewVarsForView()
    {
        $path = '/var/www/html/plugins/bc-front/templates';
        //テスト前の準備
        $param = [
            'fullpath' => '/var/www/html/plugins/bc-front/templates',
            'path' => '/var/www/html/plugins/bc-front/templates',
            'plugin' => 'bc-front',
            'theme' => 'bc-front',
            'type' => 'folder',
        ];
        //対象メソッドをコール
        $rs = $this->ThemeFoldersAdminService->getViewVarsForView(
            $this->ThemeFoldersAdminService->get($path),
            $this->ThemeFoldersAdminService->getForm([]),
            $param
        );
        //戻る値を確認
        $this->assertArrayHasKey('themeFolderForm', $rs);
        $this->assertArrayHasKey('themeFolder', $rs);
        $this->assertNotNull($rs['currentPath']);
        $this->assertNotNull($rs['theme']);
        $this->assertNotNull($rs['plugin']);
        $this->assertNotNull($rs['type']);
        $this->assertNotNull($rs['path']);
        $this->assertTrue($rs['isWritable']);
        $this->assertEquals($rs['pageTitle'], 'Bc-front｜フォルダ表示');
    }
}
