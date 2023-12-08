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

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Controller\Admin\ThemesController;
use BaserCore\Test\Scenario\SmallSetContentFoldersScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcFolder;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Composer\Package\Archiver\ZipArchiver;

/**
 * Class ThemesControllerTest
 * @property ThemesController $ThemesController;
 */
class ThemesControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;
    use BcContainerTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemesController = new ThemesController($this->getRequest());
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->getRequest('/baser/admin/baser-core/themes/');
        $this->loginAdmin($request);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * テーマをアップロードして適用する
     */
    public function test_add()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $path = ROOT . DS . 'plugins' . DS . 'BcPluginSample';
        $zipSrcPath = TMP . 'zip' . DS;
        $theme = 'BcPluginSample2';
        $folder = new BcFolder($zipSrcPath);
        $folder->create();
        $folder->copy($path, $zipSrcPath. 'BcPluginSample2');
        $zip = new ZipArchiver();
        $testFile = $zipSrcPath . $theme . '.zip';
        $zip->archive($zipSrcPath, $testFile, true);

        $this->setUploadFileToRequest('file', $testFile);
        $this->setUnlockedFields(['file']);
        $this->post('/baser/admin/baser-core/themes/add');

        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'themes',
            'action' => 'index'
        ]);
        $this->assertFlashMessage('テーマファイル「' . $theme . '」を追加しました。');

        (new BcFolder(ROOT . DS . 'plugins' . DS . $theme))->delete();
        (new BcFolder($zipSrcPath))->delete();
    }

    /**
     * baserマーケットのテーマデータを取得する
     */
    public function test_get_market_themes()
    {
        $this->markTestIncomplete('baserマーケットのRSSのロードに時間がかかり過ぎるためスキップ。マーケット側を見直してから対応する');
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->get('/baser/admin/baser-core/themes/get_market_themes');
        $this->assertResponseContains('<p class="theme-name">');
        $this->assertResponseOk();
    }

    /**
     * テーマ一覧
     */
    public function test_index()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->get('/baser/admin/baser-core/themes/index');
        $this->assertResponseOk();
    }

    /**
     * 初期データセットを読み込む
     */
    public function test_load_default_data_pattern()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * テーマをコピーする
     */
    public function test_copy()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        // notFound
        $this->post('/baser/admin/baser-core/themes/copy/');
        $this->assertResponseCode(404);

        // 正常にコピーする
        $theme = 'BcFront';
        $this->post('/baser/admin/baser-core/themes/copy/' . $theme);
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'themes',
            'action' => 'index'
        ]);
        $this->assertFlashMessage("テーマ「"  . $theme . "」をコピーしました。");

        // コピーしたテーマを削除する
        $path = BASER_THEMES . $theme . 'Copy';
        (new BcFolder($path))->delete();
    }

    /**
     * テーマを削除する
     */
    public function test_delete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        // notFound
        $this->post('/baser/admin/baser-core/themes/delete/');
        $this->assertResponseCode(404);
        // テーマをコピーする
        $theme = 'BcFront';
        $this->post('/baser/admin/baser-core/themes/copy/' . $theme);
        $this->assertResponseCode(302);
        // テーマを削除する
        $themeCopy = $theme . 'Copy';
        $this->post('/baser/admin/baser-core/themes/delete/' . $themeCopy);
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'themes',
            'action' => 'index'
        ]);
        $this->assertFlashMessage("テーマ「"  . $themeCopy . "」を削除しました。");
    }

    /**
     * テーマを適用する
     */
    public function test_apply()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(SmallSetContentFoldersScenario::class);
        $theme = 'BcFront';
        $this->post('/baser/admin/baser-core/themes/apply/' . $theme);
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'themes',
            'action' => 'index'
        ]);
        $this->assertFlashMessage("テーマ「BcFront」を適用しました。\n\nこのテーマは初期データを保有しています。\nWebサイトにテーマに合ったデータを適用するには、初期データ読込を実行してください。");
    }


    /**
     * 初期データセットをダウンロードする
     */
    public function test_download_default_data_pattern()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // TODO 2022/09/16 ryuring
        // 内部的に header 関数を使うため、Cannot modify header information が出たり、
        // ダウンロードする zip の内容が出力されてしまう。
        // @runInSeparateProcess を使うことで header 対応できるという情報があったが、
        // こちらを使うと、なぜか、sites テーブルの id が重複するというエラーになってしまう。
        // 一旦、スキップにしておく
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->get('/baser/admin/baser-core/themes/download_default_data_pattern');
        $this->assertResponseOk();

        $tmpDir = TMP . 'csv' . DS;
        $folder = new BcFolder($tmpDir);
        $folderContents = $folder->getFolders(['full'=>true]);
        $result = true;
        if (count($folderContents) > 0) $result = false;
        foreach ($folderContents as $path) {
            $childFolder = new BcFolder($path);
            $childFiles = $childFolder->getFiles();
            if (count($childFiles) > 0) $result = false;
        }

        $this->assertTrue($result);
    }

    /**
     * ダウンロード
     */
    public function test_download()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * スクリーンショットを表示
     */
    public function test_screenshot()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->get('/baser/admin/baser-core/themes/screenshot/BcFront');
        $this->assertResponseOk();
        $this->assertFileResponse(ROOT . '/plugins/bc-front/screenshot.png');

        $this->get('/baser/admin/baser-core/themes/screenshot/NotExistsTheme');
        $this->assertResponseError();
    }

}

