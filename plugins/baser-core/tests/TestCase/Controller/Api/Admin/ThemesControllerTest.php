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

namespace BaserCore\Test\TestCase\Controller\Api\Admin;

use BaserCore\Service\ThemesService;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SmallSetContentFoldersScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFolder;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Composer\Package\Archiver\ZipArchiver;

class ThemesControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * Access Token
     * @var string
     */
    public $accessToken = null;

    /**
     * Refresh Token
     * @var null
     */
    public $refreshToken = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        SiteFactory::make(['id' => 1, 'theme' => 'BcThemeSample'])->persist();
        UserFactory::make()->admin()->persist();
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testView()
    {
        $this->get('/baser/api/admin/baser-core/themes/view/BcFront.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('BcFront', $result->theme->name);
    }
    /**
     * test View
     */
    public function testIndex(): void
    {
        $this->get('/baser/api/admin/baser-core/themes/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());

        $this->assertCount(3, $result->themes);
        $this->assertEquals('BcColumn', $result->themes[0]->name);
        $this->assertEquals('BcThemeSample', $result->themes[1]->name);
    }

    /**
     * test Add
     */
    public function testAdd(): void
    {
        $this->get('/baser/api/admin/baser-core/themes/add.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $path = ROOT . DS . 'plugins' . DS . 'BcPluginSample';
        $zipSrcPath = TMP  . 'zip' . DS;
        $folder = new BcFolder($zipSrcPath);
        $folder->create();
        //copy
        $folder = new BcFolder($path);
        $folder->copy($zipSrcPath. 'BcPluginSample2');
        $theme = 'BcPluginSample2';
        $zip = new ZipArchiver();
        $testFile = $zipSrcPath . $theme . '.zip';
        $zip->archive($zipSrcPath, $testFile, true);

        $this->setUploadFileToRequest('file', $testFile);
        $this->post('/baser/api/admin/baser-core/themes/add.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($theme, $result->theme);
        $this->assertEquals('テーマファイル「' . $theme . '」を追加しました。', $result->message);

        (new BcFolder(ROOT . DS . 'plugins' . DS . $theme))->delete();
        (new BcFolder($zipSrcPath))->delete();
    }
    /**
     * test copy
     * @return void
     */
    public function testDelete()
    {
        $this->get('/baser/api/admin/baser-core/themes/delete/BcPluginSampleTest.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $themeService = new ThemesService();
        $themeService->copy('BcPluginSample');
        $this->post('/baser/api/admin/baser-core/themes/delete/BcPluginSampleCopy.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('テーマ「BcPluginSampleCopy」を削除しました。', $result->message);

        $this->post('/baser/api/admin/baser-core/themes/delete/BcPluginSampleCopy.json?token=' . $this->accessToken);
        $this->assertResponseCode(500);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データベース処理中にエラーが発生しました。 に書込み権限がありません。', $result->message);
    }

    /**
     * test copy
     * @return void
     */
    public function testCopy()
    {
        $this->get('/baser/api/admin/baser-core/themes/copy/BcPluginSample.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $this->post('/baser/api/admin/baser-core/themes/copy/BcPluginSample2.json?token=' . $this->accessToken);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('テーマ「BcPluginSample2」のコピーに失敗しました。', $result->message);

        $this->post('/baser/api/admin/baser-core/themes/copy/BcPluginSample.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('テーマ「BcPluginSample」をコピーしました。', $result->message);
        $themeService = new ThemesService();
        $themeService->delete('BcPluginSampleCopy');
    }

    /**
     * test loadDefaultDataPattern
     */
    public function testLoadDefaultData()
    {
        $this->get('/baser/api/admin/baser-core/themes/load_default_data.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $this->post('/baser/api/admin/baser-core/themes/load_default_data.json?token=' . $this->accessToken);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('不正な操作です。', $result->message);

        $this->post('/baser/api/admin/baser-core/themes/load_default_data.json?token=' . $this->accessToken, ['default_data_pattern' => 'BcThemeSample.default']);
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('初期データの読み込みが完了しました。', $result->message);

        $this->post('/baser/api/admin/baser-core/themes/load_default_data.json?token=' . $this->accessToken, ['default_data_pattern' => 'BcThemeSample.default2']);
        $this->assertResponseCode(500);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データベース処理中にエラーが発生しました。初期データのバージョンが違うか、初期データの構造が壊れています。', $result->message);
    }

    /**
     * テーマを適用するAPI
     */
    public function testApply(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $theme = 'BcColumn';
        $this->loadFixtureScenario(SmallSetContentFoldersScenario::class);
        $this->post('/baser/api/admin/baser-core/themes/apply/1/'. $theme . '.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($theme, $result->theme->name);
        $this->assertStringContainsString('テーマ「' . $theme . '」を適用しました。', $result->message);
    }

    /**
     * test get_market_themes
     * @return void
     */
    public function test_get_market_themes()
    {
        $this->markTestIncomplete('baserマーケットのRSSのロードに時間がかかり過ぎるためスキップ。マーケット側を見直してから対応する');
        $this->post('/baser/api/admin/baser-core/themes/get_market_themes.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(true, count($result->baserThemes) > 0);
    }
}
