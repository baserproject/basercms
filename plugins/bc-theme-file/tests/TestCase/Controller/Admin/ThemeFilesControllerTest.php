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

namespace BcThemeFile\Test\TestCase\Controller\Admin;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcFile;
use BcThemeFile\Controller\Admin\ThemeFilesController;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class ThemeFilesControllerTest
 *
 * @property  ThemeFilesController $ThemeFilesController
 */
class ThemeFilesControllerTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->ThemeFilesController = new ThemeFilesController($this->loginAdmin($this->getRequest()));
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ThemeFilesController);
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $_tempalteTypesExpected = [
            'Layouts' => 'レイアウトテンプレート',
            'Elements' => 'エレメントテンプレート',
            'Emails' => 'Eメールテンプレート',
            'etc' => 'コンテンツテンプレート',
            'css' => 'スタイルシート',
            'js' => 'Javascript',
            'img' => 'イメージ'
        ];
        $this->assertEquals($_tempalteTypesExpected, $this->ThemeFilesController->_tempalteTypes);
    }

    /**
     * test isDefaultTheme
     */
    public function test_isDefaultTheme()
    {
        //テーマがデフォルトテーマの場合、
        $request = $this->getRequest()->withParam('pass.0', 'BcFront');
        $this->ThemeFilesController = new ThemeFilesController($this->loginAdmin($request));
        $result = $this->execPrivateMethod($this->ThemeFilesController, 'isDefaultTheme', []);
        $this->assertTrue($result);

        //テーマがデフォルトテーマではないの場合、
        $request = $this->getRequest()->withParam('pass.0', 'BcColumn');
        $this->ThemeFilesController = new ThemeFilesController($this->loginAdmin($request));
        $result = $this->execPrivateMethod($this->ThemeFilesController, 'isDefaultTheme', []);
        $this->assertFalse($result);
    }

    /**
     * test beforeRender
     */
    public function test_beforeRender()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test index
     */
    public function test_index()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test add
     */
    public function test_add()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $fullpath = BASER_PLUGINS . 'bc-column' . '/templates/layout/';
        $file = new BcFile($fullpath . 'base_name_1.php');
        $file->create();

        //Postメソッドを検証場合
        $this->post('/baser/admin/bc-theme-file/theme_files/delete/BcColumn/layout/base_name_1.php');
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('ファイル base_name_1.php を削除しました。');
        $this->assertRedirect('/baser/admin/bc-theme-file/theme_files/index/BcColumn/layout/');
        //実際にファイルが削除されいてるか確認すること
        $this->assertFalse(file_exists($fullpath . 'base_name_1.php'));
    }

    /**
     * test delete_folder
     */
    public function test_delete_folder()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test view
     */
    public function test_view()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test copy
     */
    public function test_copy()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test copy_folder
     */
    public function test_copy_folder()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test upload
     */
    public function test_upload()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test add_folder
     */
    public function test_add_folder()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test edit_folder
     */
    public function test_edit_folder()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test view_folder
     */
    public function test_view_folder()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test copy_to_theme
     */
    public function test_copy_to_theme()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test copy_folder_to_theme
     */
    public function test_copy_folder_to_theme()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test img
     */
    public function test_img()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test img_thumb
     */
    public function test_img_thumb()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test parseArgs
     */
    public function test_parseArgs()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }
}
