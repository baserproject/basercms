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

namespace BcThemeFile\Test\TestCase\Controller\Api;

use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class ThemeFilesControllerTest extends BcTestCase
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
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        SiteFactory::make(['id' => 1, 'status' => true, 'theme' => 'BcSpaSample'])->persist();
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
     * [API] テーマファイル ファイル新規追加
     */
    public function test_add()
    {
        //POSTデータを生成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        $data = [
            'theme' => 'BcThemeSample',
            'type' => 'layout',
            'path' => '',
            'base_name' => 'base_name_1',
            'contents' => 'this is a content!',
            'ext' => 'php',
            'plugin' => 'BaserCore'
        ];
        //APIをコール
        $this->post('/baser/api/bc-theme-file/theme_files/add.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ファイル「base_name_1.php」を作成しました。', $result->message);
        $this->assertEquals($fullpath . 'base_name_1.php', $result->entity->fullpath);
        //実際にファイルが作成されいてるか確認すること
        $this->assertTrue(file_exists($fullpath . 'base_name_1.php'));
        //作成されたファイルを削除
        unlink($fullpath . 'base_name_1.php');
    }

    /**
     * [API] テーマファイル ファイル編集
     */
    public function test_edit()
    {
        //POSTデータを生成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        new File($fullpath . 'base_name_1.php', true);
        new File($fullpath . 'Admin/default.php', true);
        $data = [
            'theme' => 'BcThemeSample',
            'type' => 'layout',
            'path' => 'base_name_1.php',
            'base_name' => 'base_name_2',
            'contents' => 'this is a content changed!',
            'ext' => 'php',
            'plugin' => 'BaserCore'
        ];
        //APIをコール
        $this->post('/baser/api/bc-theme-file/theme_files/edit.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ファイル「base_name_2.php」を更新しました。', $result->message);
        $this->assertEquals($fullpath . 'base_name_2.php', $result->entity->fullpath);
        //実際にファイルが変更されいてるか確認すること
        $this->assertTrue(file_exists($fullpath . 'base_name_2.php'));
        //ファイルの中身を確認
        $this->assertEquals('this is a content changed!' , file_get_contents($fullpath . 'base_name_2.php'));
        //変更した前にファイル名が存在しないか確認すること
        $this->assertFalse(file_exists($fullpath . 'base_name_1.php'));

        //path が、Admin/default.php と階層化されている場合、
        $data = [
            'theme' => 'BcThemeSample',
            'type' => 'layout',
            'path' => 'Admin/default.php',
            'base_name' => 'default_changed',
            'contents' => 'this is a content changed!',
            'ext' => 'php',
            'plugin' => 'BaserCore'
        ];
        //APIをコール
        $this->post('/baser/api/bc-theme-file/theme_files/edit.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ファイル「default_changed.php」を更新しました。', $result->message);
        $this->assertEquals($fullpath . 'Admin/default_changed.php', $result->entity->fullpath);
        //実際にファイルが変更されいてるか確認すること
        $this->assertTrue(file_exists($fullpath . 'Admin/default_changed.php'));
        //ファイルの中身を確認
        $this->assertEquals('this is a content changed!' , file_get_contents($fullpath . 'Admin/default_changed.php'));
        //変更した前にファイル名が存在しないか確認すること
        $this->assertFalse(file_exists($fullpath . 'Admin/default.php'));

        //作成されたファイルを削除
        unlink($fullpath . 'base_name_2.php');
        unlink($fullpath . 'Admin/default_changed.php');
    }

    /**
     * [API] テーマファイル ファイル削除
     */
    public function test_delete()
    {
        //テストファイルを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        new File($fullpath . 'base_name_1.php', true);
        //POSTデータを生成
        $data = [
            'theme' => 'BcThemeSample',
            'type' => 'layout',
            'plugin' => 'BaserCore',
            'path' => 'base_name_1.php'
        ];
        //APIをコール
        $this->post('/baser/api/bc-theme-file/theme_files/delete.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ファイル「base_name_1.php」を削除しました。', $result->message);
        //実際にファイルが削除されいてるか確認すること
        $this->assertFalse(file_exists($fullpath . 'base_name_1.php'));

        //もう一度APIをコールする場合、エラーを出る
        $this->post('/baser/api/bc-theme-file/theme_files/delete.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ファイル「base_name_1.php」の削除に失敗しました。', $result->message);
    }

    /**
     * [API] テーマファイル ファイルコピー
     */
    public function test_copy()
    {
        //テストファイルを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        new File($fullpath . 'base_name_1.php', true);
        //POSTデータを生成
        $data = [
            'theme' => 'BcThemeSample',
            'type' => 'layout',
            'plugin' => 'BaserCore',
            'path' => 'base_name_1.php',
        ];
        //APIをコール
        $this->post('/baser/api/bc-theme-file/theme_files/copy.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ファイル「base_name_1.php」をコピーしました。', $result->message);
        //実際にファイルが削除されいてるか確認すること
        $this->assertTrue(file_exists($fullpath . 'base_name_1_copy.php'));
        //生成されたテストファイルを削除
        unlink($fullpath . 'base_name_1.php');
        unlink($fullpath . 'base_name_1_copy.php');
    }

    /**
     * [API] テーマファイル 現在のテーマにファイルをコピー
     */
    public function test_copy_to_theme()
    {
        //POSTデータを生成
        $fullpath = BASER_PLUGINS . 'bc-front' . '/templates/layout/';
        new File($fullpath . 'base_name_1.php', true);
        $data = [
            'theme' => 'BcFront',
            'type' => 'layout',
            'path' => 'base_name_1.php',
            'plugin' => 'BaserCore'
        ];
        //APIをコール
        $this->post('/baser/api/bc-theme-file/theme_files/copy_to_theme.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(
            'コアファイル base_name_1.php を テーマ BcSpaSample の次のパスとしてコピーしました。\n/plugins/BcSpaSample/templates/layout/base_name_1.php。',
            $result->message
        );
        //実際にファイルが作成されいてるか確認すること
        $this->assertTrue(file_exists(BASER_PLUGINS . 'BcSpaSample/templates/layout/base_name_1.php'));
        //作成したファイルを削除する
        unlink(BASER_PLUGINS . 'BcSpaSample/templates/layout/base_name_1.php');
        unlink($fullpath . 'base_name_1.php');
    }

    /**
     * [API] テーマファイル ファイルを表示
     */
    public function test_view()
    {
        //POSTデータを生成
        $data = [
            'theme' => 'BcThemeSample',
            'type' => 'layout',
            'path' => 'default.php',
            'plugin' => '',
            'token' => $this->accessToken
        ];
        $query = http_build_query($data);
        //APIをコール
        $this->get('/baser/api/bc-theme-file/theme_files/view.json?' . $query);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->entity->contents);
    }

    /**
     * [API] テーマファイル 画像を表示
     */
    public function test_img()
    {
        //POSTデータを生成
        $data = [
            'theme' => 'BcFront',
            'type' => 'img',
            'plugin' => 'BaserCore',
            'path' => 'logo.png',
            'token' => $this->accessToken
        ];
        $query = http_build_query($data);
        //APIをコール
        $this->get('/baser/api/bc-theme-file/theme_files/img.json?' . $query);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull(base64_decode($result->img));
    }

    /**
     * [API] テーマファイル 画像のサムネイルを表示
     */
    public function test_img_thumb()
    {
        //POSTデータを生成
        $data = [
            'theme' => 'BcFront',
            'type' => 'img',
            'plugin' => 'BaserCore',
            'path' => 'logo.png',
            'width' => 100,
            'height' => 100,
            'token' => $this->accessToken
        ];
        $query = http_build_query($data);
        //APIをコール
        $this->get('/baser/api/bc-theme-file/theme_files/img_thumb.json?' . $query);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull(base64_decode($result->imgThumb));
    }

    /**
     * [API] テーマファイルAPI テーマファイルアップロード
     */
    public function test_upload()
    {
        //テストテーマフォルダを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        (new Folder())->create($fullpath . 'new_folder', 0777);

        //テストファイルを作成
        $filePath = TMP  . 'test_upload' . DS;
        (new Folder())->create($filePath, 0777);
        $testFile = $filePath . 'uploadTestFile.html';
        new File($testFile, true);

        //Postデータを生成
        $data = [
            'theme' => 'BcThemeSample',
            'plugin' => 'BaserCore',
            'type' => 'layout',
            'path' => 'new_folder',
        ];
        $this->setUploadFileToRequest('file', $testFile);
        $this->setUnlockedFields(['file']);
        //APIをコール
        $this->post('/baser/api/bc-theme-file/theme_files/upload.json?token=' . $this->accessToken, $data);
        //レスポンスコードを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('アップロードに成功しました。', $result->message);
        //実際にファイルが存在するか確認すること
        $this->assertTrue(file_exists($fullpath . 'new_folder/uploadTestFile.html'));

        //テストファイルとフォルダを削除
        rmdir($filePath);
        unlink($fullpath . 'new_folder/uploadTestFile.html');
        rmdir($fullpath . 'new_folder');
    }
}
