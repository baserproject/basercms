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
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class ThemeFoldersControllerTest extends BcTestCase
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
        $this->get('/baser/api/bc-theme-file/theme_folders/index.json?' . $query);
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
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * [API] テーマフォルダ テーマフォルダ編集
     */
    public function test_edit()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * [API] テーマフォルダ テーマフォルダ削除
     */
    public function test_delete()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * [API] テーマフォルダ テーマフォルダコピー
     */
    public function test_copy()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * [API] テーマフォルダ テーマフォルダアップロード
     */
    public function test_upload()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * [API] テーマフォルダ テーマフォルダコピー
     */
    public function test_copy_to_theme()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * [API] テーマフォルダ テーマフォルダを表示
     */
    public function test_view()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }
}
