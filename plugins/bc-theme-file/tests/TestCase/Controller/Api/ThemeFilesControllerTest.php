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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Filesystem\File;
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
        $this->loadFixtureScenario(InitAppScenario::class);
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
            'mode' => 'create',
            'fullpath' => $fullpath,
            'base_name' => 'base_name_1',
            'contents' => 'this is a content!',
            'ext' => 'php',
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
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * [API] テーマファイル ファイルコピー
     */
    public function test_copy()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * [API] テーマファイル 現在のテーマにファイルをコピー
     */
    public function test_copy_to_theme()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * [API] テーマファイル ファイルを表示
     */
    public function test_view()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * [API] テーマファイル 画像を表示
     */
    public function test_img()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * [API] テーマファイル 画像のサムネイルを表示
     */
    public function test_img_thumb()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }
}
