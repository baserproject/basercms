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
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

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
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Sites',
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * baserマーケットのテーマデータを取得する
     */
    public function test_get_market_themes()
    {
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * テーマを削除する
     */
    public function test_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * テーマを適用する
     */
    public function test_apply()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

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
        $this->assertFileResponse('/var/www/html/plugins/bc-front/screenshot.png');

        $this->get('/baser/admin/baser-core/themes/screenshot/NotExistsTheme');
        $this->assertResponseError();
    }

}

