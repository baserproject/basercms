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
use BaserCore\Utility\BcFolder;
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
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //実行成功場合
        $this->get('/baser/admin/bc-theme-file/theme_files/index/BcThemeSample/layout');
        //ステータスを確認
        $this->assertResponseCode(200);
        //取得データを確認
        $pageTitle = $this->_controller->viewBuilder()->getVars()['pageTitle'];
        $this->assertEquals('BcThemeSample｜レイアウトテンプレート一覧', $pageTitle);

        //異常系場合、
        $this->get('/baser/admin/bc-theme-file/theme_files/index');
        //ステータスを確認
        $this->assertResponseCode(404);
    }

    /**
     * test add
     */
    public function test_add()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //GETメソッドを検証場合
        $this->get('/baser/admin/bc-theme-file/theme_files/add/BcThemeSample/layout');
        //取得データを確認
        $pageTitle = $this->_controller->viewBuilder()->getVars()['pageTitle'];
        $this->assertEquals('BcThemeSample｜レイアウトテンプレート作成', $pageTitle);

        $postData = [
            'fullpath' => '/var/www/html/plugins/BcThemeSample/templates/layout/',
            'parent' => '/var/www/html/plugins/BcThemeSample/templates/layout/',
            'base_name' => 'test',
            'ext' => 'php',
            'contents' => 'test content',
        ];
        //Postメソッドを検証場合
        $this->post('/baser/admin/bc-theme-file/theme_files/add/BcThemeSample', $postData);
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('ファイル test.php を作成しました。');
        $this->assertRedirect(['action' => 'edit/BcThemeSample/layout/test.php']);
        unlink('/var/www/html/plugins/BcThemeSample/templates/layout/test.php');

        $postData = [
            'fullpath' => '/var/www/html/plugins/BcThemeSample/templates/layout/',
            'parent' => '/var/www/html/plugins/BcThemeSample/templates/layout/',
            'ext' => 'php',
            'contents' => 'test content',
        ];
        //エラーを発生した場合
        $this->post('/baser/admin/bc-theme-file/theme_files/add/BcThemeSample', $postData);
        //戻る値を確認
        $this->assertResponseCode(200);
        $themeFileFormVar = $this->_controller->viewBuilder()->getVar('themeFileForm');
        $this->assertEquals(
            'テーマファイル名を入力してください。',
            $themeFileFormVar->getErrors()['base_name']['_required']
        );
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $fullpath = BASER_PLUGINS . 'BcThemeSample/templates/layout/';
        $file = new BcFile($fullpath . 'base_name_1.php');
        $file->create();

        //GETメソッドを検証場合
        $this->get('/baser/admin/bc-theme-file/theme_files/edit/BcThemeSample/layout/base_name_1.php');
        //取得データを確認
        $pageTitle = $this->_controller->viewBuilder()->getVars()['pageTitle'];
        $this->assertEquals('BcThemeSample｜レイアウトテンプレート編集', $pageTitle);

        $postData = [
            'fullpath' => $fullpath . 'base_name_1.php',
            'parent' => $fullpath,
            'base_name' => 'base_name_2',
            'ext' => 'php',
            'contents' => "<?php echo 'test' ?>"
        ];
        //Postメソッドを検証場合
        $this->post('/baser/admin/bc-theme-file/theme_files/edit/BcThemeSample/layout/base_name_1.php', $postData);
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('ファイル base_name_2.php を更新しました。');
        $this->assertRedirect(['action' => 'edit/BcThemeSample/layout/base_name_2.php']);
        unlink($fullpath . 'base_name_2.php');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $fullpath = BASER_PLUGINS . 'BcColumn' . '/templates/layout/';
        $file = new BcFile($fullpath . 'base_name_1.php');
        $file->create();

        //Postメソッドを検証場合
        $this->post('/baser/admin/bc-theme-file/theme_files/delete/BcColumn/layout/base_name_1.php');
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('ファイル base_name_1.php を削除しました。');
        $this->assertRedirect('/baser/admin/bc-theme-file/theme_files/index/BcColumn/layout');
        //実際にファイルが削除されいてるか確認すること
        $this->assertFalse(file_exists($fullpath . 'base_name_1.php'));
    }

    /**
     * test delete_folder
     */
    public function test_delete_folder()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //テストテーマフォルダを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout';
        $folder = new BcFolder($fullpath . DS . 'delete_folder');
        $folder->create();
        //Postメソッドを検証場合
        $this->post('/baser/admin/bc-theme-file/theme_files/delete_folder/BcThemeSample/layout/delete_folder');
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('フォルダ delete_folder を削除しました。');
        $this->assertRedirect('/baser/admin/bc-theme-file/theme_files/index/BcThemeSample/layout');
        //実際にフォルダが削除されいてるか確認すること
        $this->assertFalse(file_exists($fullpath . 'delete_folder'));
    }

    /**
     * test view
     */
    public function test_view()
    {
        //準備
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //GETメソッドを検証場合
        $this->get('/baser/admin/bc-theme-file/theme_files/view/BcThemeSample/layout/default.php');
        //ステータスを確認
        $this->assertResponseCode(200);
        //取得データを確認
        $pageTitle = $this->_controller->viewBuilder()->getVars()['pageTitle'];
        $this->assertEquals('BcThemeSample｜レイアウトテンプレート表示', $pageTitle);

        //エラーを発生した場合
        $this->get('/baser/admin/bc-theme-file/theme_files/view/BcThemeSample/layout3/default.php');
        //ステータスを確認
        $this->assertResponseCode(404);
    }

    /**
     * test copy
     */
    public function test_copy()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $fullpath = BASER_PLUGINS . 'BcThemeSample/templates/layout/';
        //Postメソッドを検証場合
        $this->post('/baser/admin/bc-theme-file/theme_files/copy/BcThemeSample/layout/default.php');
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('ファイル default.php をコピーしました。');
        $this->assertRedirect('baser/admin/bc-theme-file/theme_files/index/BcThemeSample/layout');
        unlink($fullpath . 'default_copy.php');
    }

    /**
     * test copy_folder
     */
    public function test_copy_folder()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //テストテーマフォルダを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout';
        $folder = new BcFolder($fullpath . DS . 'new_folder');
        $folder->create();

        //Postメソッドを検証場合
        $this->post('/baser/admin/bc-theme-file/theme_files/copy_folder/BcThemeSample/layout/new_folder');
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('フォルダ new_folder をコピーしました。');
        $this->assertRedirect('baser/admin/bc-theme-file/theme_files/index/BcThemeSample/layout');

        //テスト後に不要なフォルダーを削除
        $folder->delete();
        $folder = new BcFolder($fullpath . DS . 'new_folder_copy');
        $folder->delete();
    }

    /**
     * test upload
     */
    public function test_upload()
    {
        //準備
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //テストフォルダパス
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';

        //テストファイルを作成
        $filePath = TMP . 'test_upload' . DS;
        $folder = new BcFolder($filePath);
        $folder->create();
        $testFile = $filePath . 'uploadTestFile.html';
        $tmpFile = new BcFile($testFile);
        $tmpFile->create();

        $this->setUploadFileToRequest('file', $testFile);
        $this->setUnlockedFields(['file']);

        //Postメソッドを検証場合
        $this->post('/baser/admin/bc-theme-file/theme_files/upload/BcThemeSample/layout');
        //ステータスを確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('アップロードに成功しました。');
        $this->assertRedirect('/baser/admin/bc-theme-file/theme_files/index/BcThemeSample/layout');
        //実際にファイルが存在するか確認すること
        $this->assertTrue(file_exists($fullpath . 'uploadTestFile.html'));

        //テストファイルとフォルダを削除
        unlink($fullpath . 'uploadTestFile.html');

        $folder->create();
        $tmpFile->create();
        //エラーを発生した場合
        $this->post('/baser/admin/bc-theme-file/theme_files/index/BcThemeSample333333/layout3');
        //ステータスを確認
        $this->assertResponseCode(500);
        //テストファイルとフォルダを削除
        $folder->delete();
    }

    /**
     * test add_folder
     */
    public function test_add_folder()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //GETメソッドを検証場合
        $this->get('/baser/admin/bc-theme-file/theme_files/add_folder/BcThemeSample/layout');
        //取得データを確認
        $pageTitle = $this->_controller->viewBuilder()->getVars()['pageTitle'];
        $this->assertEquals('BcThemeSample｜フォルダ作成', $pageTitle);

        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout';
        $postData = [
            'parent' => $fullpath,
            'fullpath' => $fullpath,
            'name' => 'new_folder',
        ];

        //POSTメソッドを検証場合
        $this->post('/baser/admin/bc-theme-file/theme_files/add_folder/BcThemeSample/layout', $postData);
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('フォルダ「new_folder」を作成しました。');
        $this->assertRedirect('/baser/admin/bc-theme-file/theme_files/index/BcThemeSample/layout');
        //実際にフォルダが作成されいてるか確認すること
        $this->assertTrue(is_dir($fullpath . '/new_folder'));

        //作成されたフォルダを削除
        rmdir($fullpath . '/new_folder');

        //BcFormFailedExceptionを発生した場合、
        $postData['name'] = 'ああああ';
        $this->post('/baser/admin/bc-theme-file/theme_files/add_folder/BcThemeSample/layout', $postData);
        //戻る値を確認
        $this->assertResponseCode(200);
        $themeFolderForm = $this->_controller->viewBuilder()->getVar('themeFolderForm');
        $this->assertEquals(
            'テーマフォルダー名は半角英数字とハイフン、アンダースコアのみが利用可能です。',
            $themeFolderForm->getErrors()['name']['nameAlphaNumericPlus']
        );
    }

    /**
     * test edit_folder
     */
    public function test_edit_folder()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //テストテーマフォルダを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout';
        $folder = new BcFolder($fullpath . DS . 'new_folder');
        $folder->create();

        //GETメソッドを検証場合
        $this->get('/baser/admin/bc-theme-file/theme_files/edit_folder/BcThemeSample/layout/new_folder');
        //ステータスを確認
        $this->assertResponseCode(200);
        //取得データを確認
        $pageTitle = $this->_controller->viewBuilder()->getVars()['pageTitle'];
        $this->assertEquals('BcThemeSample｜フォルダ編集', $pageTitle);

        //Postデータを生成
        $data = [
            'parent' => $fullpath,
            'fullpath' => $fullpath . DS . 'new_folder',
            'name' => 'edit_folder',
        ];
        $this->post('/baser/admin/bc-theme-file/theme_files/edit_folder/BcThemeSample/layout/new_folder', $data);
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('フォルダ名を edit_folder に変更しました。');
        $this->assertRedirect('baser/admin/bc-theme-file/theme_files/index/BcThemeSample/layout/.');
        //実際にフォルダが変更されいてるか確認すること
        $this->assertTrue(is_dir($fullpath . DS . 'edit_folder'));
        //変更前のフォルダが存在しないか確認すること
        $this->assertFalse(is_dir($fullpath . DS . 'new_folder'));

        //BcFormFailedExceptionを発生した場合、
        $data['name'] = 'ああああ';
        $this->post('/baser/admin/bc-theme-file/theme_files/edit_folder/BcThemeSample/layout/new_folder', $data);
        //戻る値を確認
        $this->assertResponseCode(200);
        $themeFolderForm = $this->_controller->viewBuilder()->getVar('themeFolderForm');
        $this->assertEquals(
            'テーマフォルダー名は半角英数字とハイフン、アンダースコアのみが利用可能です。',
            $themeFolderForm->getErrors()['name']['nameAlphaNumericPlus']
        );

        //変更されたフォルダを削除
        rmdir($fullpath . '/edit_folder');
    }

    /**
     * test view_folder
     */
    public function test_view_folder()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //テストテーマフォルダを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout';
        $folder = new BcFolder($fullpath . DS . 'new_folder');
        $folder->create();

        //GETメソッドを検証場合
        $this->get('/baser/admin/bc-theme-file/theme_files/view_folder/BcThemeSample/layout/new_folder');
        //ステータスを確認
        $this->assertResponseCode(200);
        //取得データを確認
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals('BcThemeSample｜フォルダ表示', $vars['pageTitle']);
        $this->assertEquals('/plugins/BcThemeSample/templates/layout/', $vars['currentPath']);
        $this->assertEquals('BcThemeSample', $vars['theme']);
        $this->assertArrayHasKey('themeFolderForm', $vars);
        $this->assertArrayHasKey('themeFolder', $vars);
        $this->assertArrayHasKey('isWritable', $vars);
        $this->assertArrayHasKey('plugin', $vars);
        $this->assertArrayHasKey('type', $vars);
        $this->assertArrayHasKey('path', $vars);

        //不要フォルダーを削除
        $folder->delete();
    }

    /**
     * test copy_to_theme
     */
    public function test_copy_to_theme()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データを生成
        $fullpath = BASER_PLUGINS . 'bc-front' . '/templates/layout/';
        $file = new BcFile($fullpath . 'base_name_1.php');
        $file->create();

        $this->post('/baser/admin/bc-theme-file/theme_files/copy_to_theme/BcFront/layout/base_name_1.php');
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('コアファイル base_name_1.php を テーマ BcFront の次のパスとしてコピーしました。
/plugins/bc-front/templates/plugin//layout/base_name_1.php');

        //不要ファイルを削除
        unlink(BASER_PLUGINS . 'bc-front' . '/templates/layout/base_name_1.php');
        unlink(BASER_PLUGINS . 'bc-front' . '/templates/plugin/layout/base_name_1.php');

        //異常系テスト：存在しないファイルをコピーする場合、
        $this->post('/baser/admin/bc-theme-file/theme_files/copy_to_theme/BcColumn/layout/base_name_1.php');
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('コアファイル base_name_1.php のコピーに失敗しました。');
    }

    /**
     * test copy_folder_to_theme
     */
    public function test_copy_folder_to_theme()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データを生成
        $fullpath = BASER_PLUGINS . 'bc-front' . '/templates/layout/testCopy';
        $newFolder = new BcFolder($fullpath);
        $newFolder->create();

        //フォルダがコピーできる場合、
        $this->post('/baser/admin/bc-theme-file/theme_files/copy_folder_to_theme/BcFront/layout/testCopy');
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('コアフォルダ testCopy を テーマ BcFront の次のパスとしてコピーしました。
/plugins/bc-front/templates/layout/testCopy/');

        //不要フォルダを削除
        $newFolder->delete();
        $copyFolder = new BcFolder(BASER_PLUGINS . 'bc-front' . '/templates/plugin/layout/testCopy');
        $copyFolder->delete();

        //異常系テスト：存在しないファイルをコピーする場合、
        $this->post('/baser/admin/bc-theme-file/theme_files/copy_folder_to_theme/BcFront/layout/testCopy');        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('コアフォルダ testCopy のコピーに失敗しました。');
    }

    /**
     * test img
     */
    public function test_img()
    {
        // TODO header を出力するためのエラーが発生するためコメントアウト
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //GETメソッドを検証場合
        $this->get('/baser/admin/bc-theme-file/theme_files/img/BcColumn/img/logo.png');
        //取得データを確認
        $this->assertNotNull($this->_controller->getResponse());
    }

    /**
     * test img_thumb
     */
    public function test_img_thumb()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //GETメソッドを検証場合
        $this->get('/baser/admin/bc-theme-file/theme_files/img_thumb/BcColumn/img/logo.png');
        //取得データを確認
        $this->assertNotNull($this->_controller->getResponse());
    }

    /**
     * test parseArgs
     * @dataProvider parseArgsDataProvider
     */
    public function test_parseArgs($args, $expected)
    {
        $rs = $this->execPrivateMethod($this->ThemeFilesController, 'parseArgs', [$args]);
        $this->assertEquals($rs['fullpath'], $expected);
    }

    public static function parseArgsDataProvider()
    {
        return [
            [
                [
                    'BcThemeSample',
                    'layout',
                    'new_folder',
                ],
                '/var/www/html/plugins/BcThemeSample/templates/layout/new_folder'
            ],
            [
                [
                    'BaserCore',
                    'BcThemeSample',
                    'layout',
                    'new_folder',
                ],
                '/var/www/html/plugins/BcThemeSample/templates/layout/new_folder'
            ],
            [
                [
                    'BcBlog',
                    'BcThemeSample',
                    'layout',
                    'new_folder',
                ],
                '/var/www/html/plugins/BcThemeSample/templates/layout/new_folder'
            ],
            [
                [
                    'BaserCore',
                    'BcThemeSample',
                    'layout',
                    'new_folder',
                    'default.php',
                ],
                '/var/www/html/plugins/BcThemeSample/templates/layout/new_folder/default.php'
            ]
        ];
    }
}
