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

use BaserCore\Test\Factory\PluginFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\Controller\Admin\PluginsController;
use Cake\Event\Event;
use Composer\Package\Archiver\ZipArchiver;

/**
 * Class PluginsControllerTest
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
        $this->setFixtureTruncate();
        parent::setUp();
        $this->PluginsController = new PluginsController($this->loginAdmin($this->getRequest()));
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->truncateTable('blog_categories');
        $this->truncateTable('blog_contents');
        $this->truncateTable('blog_posts');
        $this->truncateTable('blog_tags');
        $this->truncateTable('blog_posts_blog_tags');
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
        Configure::write('BcRequest.isUpdater', true);
        $event = new Event('Controller.beforeFilter', $this->PluginsController);
        $this->PluginsController->beforeFilter($event);
        $this->assertEquals($this->PluginsController->Security->getConfig('unlockedActions'), ['reset_db', 'update_sort', 'batch']);
        $this->assertEquals(['update'], $this->PluginsController->Authentication->getUnauthenticatedActions());
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
        $this->put('/baser/admin/baser-core/plugins/install/BcBlog', $data);
    }


    /**
     * test update
     */
    public function testUpdate(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $path = Plugin::path('BcSpaSample');
        rename($path . 'VERSION.txt', $path . 'VERSION.bak.txt');
        $file = new File($path . 'VERSION.txt');
        $file->write('0.0.2');
        $file->close();
        PluginFactory::make(['name' => 'BcSpaSample', 'version' => '0.0.1'])->persist();
        $this->put('/baser/admin/baser-core/plugins/update/BcSpaSample', [
            'connection' => 'test',
            'update' => 1
        ]);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'plugins',
            'action' => 'update',
            'BcSpaSample'
        ]);
        $this->assertFlashMessage('アップデート処理が完了しました。画面下部のアップデートログを確認してください。');
        rename($path . 'VERSION.bak.txt', $path . 'VERSION.txt');
    }

    /**
     * test update core
     */
    public function testUpdateCore(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        rename(BASER . 'VERSION.txt', BASER . 'VERSION.bak.txt');
        $file = new File(BASER . 'VERSION.txt');
        $file->write('10.0.0');
        $file->close();
        $this->put('/update', [
            'connection' => 'test',
            'update' => 1
        ]);
        $this->assertRedirect('/');
        $this->assertFlashMessage(sprintf('全てのアップデート処理が完了しました。 %s にログを出力しています。', LOGS . 'update.log'));
        rename(BASER . 'VERSION.bak.txt', BASER . 'VERSION.txt');
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
        Plugin::getCollection()->remove('BcBlog');
        $this->put('/baser/admin/baser-core/plugins/install/BcBlog', $data);
    }

    /**
     * test add
     */
    public function test_add()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $path = BASER_PLUGINS . 'BcSpaSample';
        $zipSrcPath = TMP . 'zip' . DS;
        $folder = new Folder();
        $folder->create($zipSrcPath, 0777);
        $folder->copy($zipSrcPath . 'BcSpaSample2', ['from' => $path, 'mode' => 0777]);
        $plugin = 'BcSpaSample2';
        $zip = new ZipArchiver();
        $testFile = $zipSrcPath . $plugin . '.zip';
        $zip->archive($zipSrcPath, $testFile, true);

        $this->setUploadFileToRequest('file', $testFile, '', 1);
        $this->setUnlockedFields(['file']);
        $this->post('/baser/admin/baser-core/plugins/add');
        $this->assertResponseCode(302);
        $this->assertFlashMessage('ファイルのアップロードに失敗しました。Cannot retrieve stream due to upload error: The uploaded file exceeds the upload_max_filesize directive in php.ini');

        $this->setUploadFileToRequest('file', $testFile);
        $this->setUnlockedFields(['file']);
        $this->post('/baser/admin/baser-core/plugins/add');

        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'plugins',
            'action' => 'index'
        ]);
        $this->assertFlashMessage('新規プラグイン「' . $plugin . '」を追加しました。');

        $folder = new Folder();
        $folder->delete(BASER_PLUGINS . $plugin);
        $folder->delete($zipSrcPath);
    }
}
