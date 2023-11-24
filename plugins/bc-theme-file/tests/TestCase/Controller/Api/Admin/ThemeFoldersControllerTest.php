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

namespace BcThemeFile\Test\TestCase\Controller\Api\Admin;

use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFolder;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class ThemeFoldersControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        //現在のテーマを設定する
        SiteFactory::make(['id' => 1, 'status' => true, 'theme' => 'BcPluginSample'])->persist();
        UserFactory::make()->admin()->persist();

        $token = $this->apiLoginAdmin();
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
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
     * test batch
     */
    public function test_batch()
    {
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        (new BcFolder($fullpath . 'delete_folder'))->create();
        //APIをコール
        $this->post('/baser/api/admin/bc-theme-file/theme_folders/batch.json?token=' . $this->accessToken,
            [
                'batch' => 'delete',
                'batch_targets' => [$fullpath]
            ]);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('一括処理が完了しました。', $result->message);
        //実際にフォルダが削除されいてるか確認すること
        $this->assertFalse(file_exists($fullpath . 'delete_folder'));

        //$allowMethodは削除ではない場合、
        $this->post('/baser/api/admin/bc-theme-file/theme_folders/batch.json?token=' . $this->accessToken,
            [
                'batch' => 'create',
                'batch_targets' => [$fullpath]
            ]);
        $this->assertResponseCode(500);
    }

    /**
     * [API] テーマフォルダ 一覧取得
     */
    public function test_index()
    {
        //POSTデータを生成
        $data = [
            'plugin' => '',
            'theme' => 'BcFront',
            'type' => 'img',
            'path' => '',
            'assets' => false,
            'token' => $this->accessToken
        ];
        $query = http_build_query($data);
        //APIをコール
        $this->get('/baser/api/admin/bc-theme-file/theme_folders/index.json?' . $query);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->themeFiles);
    }

    /**
     * [API] テーマフォルダ テーマフォルダ新規追加
     */
    public function test_add()
    {
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        $data = [
            'theme' => 'BcThemeSample',
            'parent' => '/var/www/html/plugins/BcThemeSample/templates/layout/',
            'plugin' => 'BaserCore',
            'type' => 'layout',
            'path' => '',
            'name' => 'new_folder',
        ];
        //APIをコール
        $this->post('/baser/api/admin/bc-theme-file/theme_folders/add.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        //themeFolderを確認
        $this->assertEquals($fullpath . '/new_folder', $result->themeFolder->fullpath);
        //メッセージを確認
        $this->assertEquals('フォルダ「/new_folder」を作成しました。', $result->message);
        //実際にフォルダが作成されいてるか確認すること
        $this->assertTrue(is_dir($fullpath . 'new_folder'));
        //作成されたフォルダを削除
        rmdir($fullpath . 'new_folder');
    }

    /**
     * [API] テーマフォルダ テーマフォルダ編集
     */
    public function test_edit()
    {
        //テストテーマフォルダを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        mkdir($fullpath . 'new_folder');
        //Postデータを生成
        $data = [
            'theme' => 'BcThemeSample',
            'parent' => '/var/www/html/plugins/BcThemeSample/templates/layout/',
            'plugin' => 'BaserCore',
            'type' => 'layout',
            'path' => 'new_folder',
            'name' => 'edit_folder',
        ];
        //APIをコール
        $this->post('/baser/api/admin/bc-theme-file/theme_folders/edit.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        //themeFolderを確認
        $this->assertEquals($fullpath . 'edit_folder', $result->themeFolder->fullpath);
        //メッセージを確認
        $this->assertEquals('フォルダ名を「edit_folder」に変更しました。', $result->message);
        //実際にフォルダが変更されいてるか確認すること
        $this->assertTrue(is_dir($fullpath . 'edit_folder'));
        //変更前のフォルダが存在しないか確認すること
        $this->assertFalse(is_dir($fullpath . 'new_folder'));
        //変更されたフォルダを削除
        rmdir($fullpath . 'edit_folder');
    }

    /**
     * [API] テーマフォルダ テーマフォルダ削除
     */
    public function test_delete()
    {
        //テストテーマフォルダを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        (new BcFolder($fullpath . 'delete_folder'))->create();
        //Postデータを生成
        $data = [
            'theme' => 'BcThemeSample',
            'parent' => '/var/www/html/plugins/BcThemeSample/templates/layout/',
            'plugin' => 'BaserCore',
            'type' => 'layout',
            'path' => 'delete_folder',
        ];
        //APIをコール
        $this->post('/baser/api/admin/bc-theme-file/theme_folders/delete.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('フォルダ「delete_folder」を削除しました。', $result->message);
        $this->assertNotNull($result->themeFolder);
        //実際にフォルダが削除されいてるか確認すること
        $this->assertFalse(file_exists($fullpath . 'delete_folder'));

        //もう一度APIをコールする場合、エラーを出る
        $this->post('/baser/api/admin/bc-theme-file/theme_folders/delete.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('フォルダ「delete_folder」の削除に失敗しました。', $result->message);
    }

    /**
     * [API] テーマフォルダ テーマフォルダコピー
     */
    public function test_copy()
    {
        //テストテーマフォルダを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        (new BcFolder($fullpath . 'new_folder'))->create();
        //Postデータを生成
        $data = [
            'theme' => 'BcThemeSample',
            'parent' => '/var/www/html/plugins/BcThemeSample/templates/layout/',
            'plugin' => 'BaserCore',
            'type' => 'layout',
            'path' => 'new_folder',
        ];
        //APIをコール
        $this->post('/baser/api/admin/bc-theme-file/theme_folders/copy.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        //メッセージを確認
        $this->assertEquals('フォルダ「new_folder」をコピーしました。', $result->message);
        //実際にフォルダが変更されいてるか確認すること
        $this->assertTrue(is_dir($fullpath . 'new_folder_copy'));
        //生成されたフォルダを削除
        rmdir($fullpath . 'new_folder');
        rmdir($fullpath . 'new_folder_copy');
    }

    /**
     * [API] テーマフォルダ テーマフォルダコピー
     */
    public function test_copy_to_theme()
    {
        //POSTデータを生成
        $fullpath = BASER_PLUGINS . '/BcPluginSample/templates/';
        $data = [
            'theme' => 'BcFront',
            'parent' => '/var/www/html/plugins/BcThemeSample/templates/layout/',
            'type' => 'Pages',
            'path' => '',
            'assets' => '',
            'plugin' => 'BaserCore'
        ];
        //APIをコール
        $this->post('/baser/api/admin/bc-theme-file/theme_folders/copy_to_theme.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(
            'コアフォルダ  を テーマ BcPluginSample の次のパスとしてコピーしました。\n/plugins/BcPluginSample/templates/Pages/。',
            $result->message
        );
        //実際にフォルダがコピーできるか確認すること
        $this->assertTrue(is_dir($fullpath . '/Pages'));
        //生成されたフォルダを削除
        unlink($fullpath . '/Pages/default.php');
        rmdir($fullpath . '/Pages');
    }

    /**
     * [API] テーマフォルダ テーマフォルダを表示
     */
    public function test_view()
    {
        //POSTデータを生成
        $data = [
            'theme' => 'BcThemeSample',
            'type' => 'layout',
            'path' => '',
            'plugin' => '',
            'token' => $this->accessToken
        ];
        $query = http_build_query($data);
        //APIをコール
        $this->get('/baser/api/admin/bc-theme-file/theme_folders/view.json?' . $query);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->entity);
    }
}
