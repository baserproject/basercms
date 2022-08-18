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

namespace BaserCore\Test\TestCase\Controller\Api;

use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Core\App;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BaserCore\Controller\Api\PluginsController Test Case
 */
class PluginsControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
    ];

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
        $this->get('/baser/api/baser-core/plugins/view/1.json?token=' . $this->accessToken);
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
        $this->get('/baser/api/baser-core/plugins/index.json?token=' . $this->accessToken);
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
    public function testInstall($pluginName, $message)
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
        $folder = new Folder($pluginPath);
        $folder->create($pluginPath, 0777);
        $this->post('/baser/api/baser-core/plugins/install/' . $pluginName .'.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($message, $result->message);
        $folder->delete($pluginPath);
    }
    public function installDataProvider()
    {
        return [
            ["BcUploader", "プラグイン「BcUploader」をインストールしました。"],
            ["UnKnown", "Plugin UnKnown could not be found."],
            ["BcTest", "プラグインに Plugin クラスが存在しません。src ディレクトリ配下に作成してください。"],
        ];
    }

    /**
     * test detach
     */
    public function testDetach()
    {
        $this->post('/baser/api/baser-core/plugins/detach/BcBlog.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('プラグイン「BcBlog」を無効にしました。', $result->message);
    }

    /**
     * test reset_db
     */
    public function testRestDb()
    {
        $this->put('/baser/api/baser-core/plugins/reset_db/BcBlog.json?token=' . $this->accessToken, ['connection' => 'test']);
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
        $this->post('/baser/api/baser-core/plugins/install/BcBlog.json?token=' . $this->accessToken, $data);
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
     * test update_sort
     */
    public function testUpdateSort()
    {
        $this->post('/baser/api/baser-core/plugins/update_sort/BcBlog.json?offset=1&token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('プラグイン「BcBlog」の並び替えを更新しました。', $result->message);
    }

    /**
     * test get_market_plugins
     */
    public function testGetMarketPlugins()
    {
        $this->post('/baser/api/baser-core/plugins/get_market_plugins.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertIsArray($result->plugins);
    }

}
