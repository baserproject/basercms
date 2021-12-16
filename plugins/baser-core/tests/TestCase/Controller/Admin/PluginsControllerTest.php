<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use Cake\Filesystem\Folder;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\Controller\Admin\PluginsController;
use Cake\Event\Event;

/**
 * Class PluginsControllerTest
 * @package BaserCore\Test\TestCase\Controller
 */
class PluginsControllerTest extends BcTestCase
{
    /**
     * IntegrationTestTrait
     */
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
        'plugin.BaserCore.Dblogs',
        'plugin.BaserCore.LoginStores',
    ];

    /**
     * PluginsController
     * @var PluginsController
     */
    public $PluginsController;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PluginsController = new PluginsController($this->loginAdmin($this->getRequest()));
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * プラグインの初期化テスト
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->PluginsController->RequestHandler);
    }

    /**
     * beforeFilterテスト
     */
    public function testBeforeFilter()
    {
        $event = new Event('Controller.beforeFilter', $this->PluginsController);
        $this->PluginsController->beforeFilter($event);
        $this->assertEquals($this->PluginsController->Security->getConfig('unlockedActions'), ['reset_db', 'update_sort', 'batch']);
    }

    /**
     * プラグインをアップロードしてインストールする
     */
    public function testAdd()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * プラグインの一覧を表示する
     */
    public function testIndex()
    {
        $this->get('/baser/admin/baser-core/plugins/index');
        $this->assertResponseOk();
    }

    /**
     * baserマーケットのプラグインデータを取得する
     */
    public function testAjax_get_market_plugins()
    {
        $this->get('/baser/admin/baser-core/plugins/get_market_plugins');
        $this->assertResponseOk();
    }

    /**
     * 並び替えを更新する
     */
    public function testAjax_update_sort()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/plugins/update_sort', ['connection' => 'test', 'Sort' => ['id' => 1, 'offset' => 1]]);
        $this->assertResponseOk();
        $this->assertSame('true', $this->_getBodyAsString());
    }

    /**
     * ファイル削除
     */
    public function testAjax_delete_file()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * プラグインを無効にして有効にして削除する
     *
     * 複数のプラグインのインストールを行うと
     * Migration ファイルの Initial クラスの重複読み込みエラーとなるので
     * 一つのプラグインで行わなければならない
     */
    public function testDetachAndInstallAndUninstall(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/plugins/detach/BcSpaSample');
        $this->assertFlashMessage('プラグインの無効化に失敗しました。');
        $this->post('/baser/admin/baser-core/plugins/detach/BcBlog');
        $this->assertFlashMessage('プラグイン「BcBlog」を無効にしました。');
        $data = [
            'connection' => 'test',
            'name' => 'BcBlog',
            'title' => 'ブログ',
            'status' => "0",
            'version' => "1.0.0",
            'permission' => "1"
        ];
        $this->put('/baser/admin/baser-core/plugins/install/BcBlog', $data);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'plugins',
            'action' => 'index'
        ]);

        $from = BcUtil::getPluginPath('BcBlog');
        $pluginDir = dirname($from);
        $folder = new Folder();
        $to = $pluginDir . DS . 'BcBlogBak';
        $folder->copy($to, [
            'from' => $from,
            'mode' => 0777
        ]);
        $folder->create($from, 0777);
        $this->post('/baser/admin/baser-core/plugins/uninstall/BcBlog', $data);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'plugins',
            'action' => 'index'
        ]);
        $this->assertFlashMessage('プラグイン「BcBlog」を削除しました。');
        $folder->move($from, [
            'from' => $to,
            'mode' => 0777,
            'schema' => Folder::OVERWRITE
        ]);

    }

    /**
     * データベースをリセットする
     */
    public function testReset_db()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->put('/baser/admin/baser-core/plugins/reset_db/BcBlog', ['connection' => 'test', 'name' => 'BcBlog']);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'plugins',
            'action' => 'install',
            'BcBlog'
        ]);
        $this->assertFlashMessage('ブログ プラグインのデータを初期化しました。');
    }

    /**
     * 一括処理できてるかテスト
     */
    public function testAjax_batch()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $batchList = [1, 2];
        $this->post('/baser/admin/baser-core/plugins/batch', ['connection' => 'test', 'ListTool' => ['batch' => 'detach', 'batch_targets' => $batchList]]);
        $this->assertResponseOk();
        $plugins = $this->getTableLocator()->get('Plugins');
        $query = $plugins->find()->select(['id', 'status']);
        // 複数detachされてるかテスト
        foreach($query as $plugin) {
            if (in_array($plugin->id, $batchList)) {
                $this->assertFalse($plugin->status);
            }
        }
        $this->assertSame('true', $this->_getBodyAsString());
    }
}
