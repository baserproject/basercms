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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\LoginStoresScenario;
use BaserCore\Test\Scenario\PermissionsScenario;
use BaserCore\Test\Scenario\PluginsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFolder;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Composer\Package\Archiver\ZipArchiver;

/**
 * BaserCore\Controller\Api\PluginsController Test Case
 */
class PluginsControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

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
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(PluginsScenario::class);
        $this->loadFixtureScenario(PermissionsScenario::class);
        $this->loadFixtureScenario(LoginStoresScenario::class);
        Configure::config('baser', new PhpConfig());
        Configure::load('BaserCore.setting', 'baser');
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
    }

    /**
     * tear Down
     */
    public function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
    }

    /**
     * test view
     */
    public function testView()
    {
        $this->get('/baser/api/admin/baser-core/plugins/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('BcBlog', $result->plugin->name);
        $this->assertEquals('ブログ', $result->plugin->title);
        $this->assertEquals('1.0.0', $result->plugin->version);
        $this->assertEquals(1, $result->plugin->priority);
        $this->assertTrue($result->plugin->status);
        $this->assertTrue($result->plugin->db_init);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/api/admin/baser-core/plugins/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('BcBlog', $result->plugins[0]->name);
    }

    /**
     * Test install
     *
     * @return void
     * @dataProvider installDataProvider
     */
    public function testInstall($pluginName, $statusCode, $message)
    {
        // フォルダはあるがインストールできない場合
        $data = [
            'connection' => 'test',
            'name' => $pluginName,
            'title' => $pluginName,
            'status' => "0",
            'version' => "1.0.0",
            'permission' => "1"
        ];
        $pluginPath = App::path('plugins')[0] . DS . 'BcTest';
        $folder = new BcFolder($pluginPath);
        $folder->create();
        $this->post('/baser/api/admin/baser-core/plugins/install/' . $pluginName .'.json?token=' . $this->accessToken, $data);
        $this->assertResponseCode($statusCode);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($message, $result->message);
        $folder->delete();
    }
    public static function installDataProvider()
    {
        return [
            ["BcUploader", 200, "プラグイン「BcUploader」をインストールしました。"],
            ["UnKnown", 500, "データベース処理中にエラーが発生しました。Plugin `UnKnown` could not be found."],
            ["BcTest", 500, "データベース処理中にエラーが発生しました。プラグインに Plugin クラスが存在しません。src ディレクトリ配下に作成してください。"],
        ];
    }

    /**
     * test detach
     */
    public function testDetach()
    {
        $this->post('/baser/api/admin/baser-core/plugins/detach/BcBlog.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('プラグイン「BcBlog」を無効にしました。', $result->message);
    }

    /**
     * test attach
     */
    public function testAttach()
    {
        $this->post('/baser/api/admin/baser-core/plugins/attach/BcBlog.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('プラグイン「BcBlog」を有効にしました。', $result->message);
        $this->assertTrue($result->plugin->status);

        $this->post('/baser/api/admin/baser-core/plugins/attach/test.json?token=' . $this->accessToken);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNull($result->plugin);
    }

    /**
     * test reset_db
     */
    public function testResetDb()
    {
        $this->put('/baser/api/admin/baser-core/plugins/reset_db/BcBlog.json?token=' . $this->accessToken, ['connection' => 'test']);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ブログ プラグインのデータを初期化しました。', $result->message);
        $plugins = $this->getTableLocator()->get('BaserCore.Plugins');
        $plugins->deleteAll(['name' => 'BcBlog']);
        $data = [
            'connection' => 'test',
            'name' => 'BcBlog',
            'title' => 'ブログ',
            'status' => "0",
            'version' => "1.0.0",
            'permission' => "1"
        ];
        $this->post('/baser/api/admin/baser-core/plugins/install/BcBlog.json?token=' . $this->accessToken, $data);
    }

    /**
     * test uninstall
     */
    public function testUninstall()
    {
        // TODO インストールの処理とまとめる予定
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test add
     */
    public function test_add()
    {
        $this->get('/baser/api/admin/baser-core/themes/add.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $path = BASER_PLUGINS . 'BcPluginSample';
        $zipSrcPath = TMP . 'zip' . DS;
        $folder = new BcFolder($zipSrcPath);
        $folder->create();
        //copy
        $folder = new BcFolder($path);
        $folder->copy($zipSrcPath . 'BcPluginSample2');
        $plugin = 'BcPluginSample2';
        $zip = new ZipArchiver();
        $testFile = $zipSrcPath . $plugin . '.zip';
        $zip->archive($zipSrcPath, $testFile, true);

        $this->setUploadFileToRequest('file', $testFile);
        $this->post('/baser/api/admin/baser-core/plugins/add.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('新規プラグイン「' . $plugin . '」を追加しました。', $result->message);

        $folder = new BcFolder(BASER_PLUGINS . $plugin);
        $folder->delete();
        $folder = new BcFolder($zipSrcPath);
        $folder->delete();
    }

    /**
     * test update_sort
     */
    public function testUpdateSort()
    {
        $this->post('/baser/api/admin/baser-core/plugins/update_sort.json?token=' . $this->accessToken, [
            'id' => 1,
            'offset' => 1
        ]);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('プラグイン「BcBlog」の並び替えを更新しました。', $result->message);
    }

    /**
     * 一括処理できてるかテスト
     */
    public function test_batch()
    {
        $batchList = [1, 2];
        $this->post('/baser/api/admin/baser-core/plugins/batch.json?token=' . $this->accessToken, [
            'batch' => 'detach',
            'batch_targets' => $batchList
        ]);
        $this->assertResponseOk();
        $plugins = $this->getTableLocator()->get('Plugins');
        $query = $plugins->find()->select(['id', 'status']);
        // 複数detachされてるかテスト
        foreach($query as $plugin) {
            if (in_array($plugin->id, $batchList)) {
                $this->assertFalse($plugin->status);
            }
        }
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('一括処理が完了しました。', $result->message);
    }

}
