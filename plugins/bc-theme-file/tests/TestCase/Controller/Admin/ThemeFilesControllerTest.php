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
use Cake\Event\Event;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class ThemeFilesControllerTest
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
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //テーマがデフォルトテーマの場合、
        $request = $this->getRequest()->withParam('pass.0', 'BcFront');
        $themeFilesController = new ThemeFilesController($this->loginAdmin($request));
        $themeFilesController->beforeRender(new Event('beforeRender'));
        $this->assertEquals(
            'デフォルトテーマのため編集できません。編集する場合は、テーマをコピーしてご利用ください。',
            $_SESSION['Flash']['flash'][0]['message']
        );

        //テーマがデフォルトテーマではないの場合、
        $request = $this->getRequest()->withParam('pass.0', 'BcColumn');
        $themeFilesController = new ThemeFilesController($this->loginAdmin($request));
        $themeFilesController->beforeRender(new Event('beforeRender'));
        $this->assertEmpty($_SESSION);

        $this->get('/baser/admin/bc-theme-file/theme_files/index/BcThemeSample');
        $isDefaultTheme = $this->_controller->viewBuilder()->getVars()['isDefaultTheme'];
        $this->assertFalse($isDefaultTheme);

        $this->get('/baser/admin/bc-theme-file/theme_files/index/BcFront');
        $isDefaultTheme = $this->_controller->viewBuilder()->getVars()['isDefaultTheme'];
        $this->assertTrue($isDefaultTheme);
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
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $fullpath = BASER_PLUGINS . 'BcColumn' . '/layout/';
        $file = new BcFile($fullpath . 'base_name_1.php');
        $file->create();

        //GETメソッドを検証場合
        $this->get('/baser/admin/bc-theme-file/theme_files/edit/BcColumn/layout/base_name_1.php');
        //取得データを確認
        $pageTitle = $this->_controller->viewBuilder()->getVars()['pageTitle'];
        $this->assertEquals('BcColumn｜レイアウトテンプレート編集', $pageTitle);

        $postData = [
            'fullpath' => '/var/www/html/plugins/BcColumn/layout/',
            'parent' => '/var/www/html/plugins/BcColumn/layout/',
            'theme' => 'BcColumn',
            'type' => 'layout',
            'path' => 'test.php',
            'base_name' => 'base_name_2',
            'contents' => 'this is a content changed!',
            'ext' => 'php',
            'plugin' => 'BaserCore'
        ];
        //Postメソッドを検証場合
        $this->post('/baser/admin/bc-theme-file/theme_files/edit/BcColumn/layout/base_name_1.php', $postData);
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('ファイル base_name_2.php を更新しました。');
        $this->assertRedirect(['action' => 'edit/layout/base_name_2.php']);
        unlink($fullpath . 'base_name_2.php');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->markTestIncomplete('このテストは未実装です。');
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
