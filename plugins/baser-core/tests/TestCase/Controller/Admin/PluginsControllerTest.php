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
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\LoginStoresScenario;
use BaserCore\Test\Scenario\PermissionsScenario;
use BaserCore\Test\Scenario\PluginsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcComposer;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\Controller\Admin\PluginsController;
use Cake\Event\Event;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
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
    use ScenarioAwareTrait;

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
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(PluginsScenario::class);
        $this->loadFixtureScenario(PermissionsScenario::class);
        $this->loadFixtureScenario(LoginStoresScenario::class);
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
        if(file_exists(ROOT . DS . 'composer.json.bak')) {
            rename(ROOT . DS . 'composer.json.bak', ROOT . DS . 'composer.json');
        }
        if(file_exists(ROOT . DS . 'composer.lock.bak')) {
            rename(ROOT . DS . 'composer.lock.bak', ROOT . DS . 'composer.lock');
        }
        if(file_exists(ROOT . DS . 'plugins' . DS . 'baser-core' . DS . 'VERSION.bak.txt')) {
            rename(
                ROOT . DS . 'plugins' . DS . 'baser-core' . DS . 'VERSION.bak.txt',
                ROOT . DS . 'plugins' . DS . 'baser-core' . DS . 'VERSION.txt'
            );
        }
    }

    /**
     * beforeFilterテスト
     */
    public function testBeforeFilter()
    {
        $event = new Event('Controller.beforeFilter', $this->PluginsController);
        $this->PluginsController->beforeFilter($event);
        $this->assertEquals($this->PluginsController->FormProtection->getConfig('unlockedActions'), ['reset_db', 'update_sort', 'batch']);
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
        $this->markTestIncomplete('このメソッドを利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
        // データが初期化されなくなってしまう。dropTableでトリガーが削除されるのが原因の様子

        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/plugins/detach/BcPluginSample');
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
        $folder = new BcFolder($from);
        $folder->create();
        $to = $pluginDir . DS . 'BcBlogBak';
        $folder->copy($to);
        $this->post('/baser/admin/baser-core/plugins/uninstall/BcBlog', $data);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'plugins',
            'action' => 'index'
        ]);
        $this->assertFlashMessage('プラグイン「BcBlog」を削除しました。');
        $folder->move($to);
        $this->put('/baser/admin/baser-core/plugins/install/BcBlog', $data);
    }


    /**
     * test update
     */
    public function testUpdate(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $path = Plugin::path('BcPluginSample');
        rename($path . 'VERSION.txt', $path . 'VERSION.bak.txt');
        $file = new BcFile($path . 'VERSION.txt');
        $file->write('0.0.2');
        PluginFactory::make(['name' => 'BcPluginSample', 'version' => '0.0.1'])->persist();
        $this->put('/baser/admin/baser-core/plugins/update/BcPluginSample', [
            'connection' => 'test',
            'update' => 1
        ]);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'plugins',
            'action' => 'update',
            'BcPluginSample'
        ]);
        $this->assertFlashMessage('アップデート処理が完了しました。画面下部のアップデートログを確認してください。');
        rename($path . 'VERSION.bak.txt', $path . 'VERSION.txt');
    }

    /**
     * test update core
     */
    public function testUpdateCore(): void
    {
        $this->markTestIncomplete('CakePHP5系対応で動作しないためスキップ。やり方の検討が必要。最新のプログラムでのテストができるようにすることを検討する。');
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        rename(BASER . 'VERSION.txt', BASER . 'VERSION.bak.txt');
        copy(ROOT . DS . 'composer.json', ROOT . DS . 'composer.json.bak');
        copy(ROOT . DS . 'composer.lock', ROOT . DS . 'composer.lock.bak');

        // replace を削除
        $file = new BcFile(ROOT . DS . 'composer.json');
        // baserCMS5.0.0が、CakePHP4.4.* に依存するため、一旦、CakePHP4.4.* に戻す
        $data = $file->read();
        $data = preg_replace('/("replace": {.+?},)/s', '' , $data);
        $data = str_replace('"cakephp/cakephp": "4.5.*"', '"cakephp/cakephp": "4.4.*"' , $data);
        $file->write($data);

        BcComposer::setup('php');
        BcComposer::update();

        $file = new BcFile(BASER . 'VERSION.txt');
        $file->write('5.0.0');
        $this->put('/baser/admin/baser-core/plugins/update', [
            'connection' => 'test',
            'update' => 1,
            'php' => '/usr/local/bin/php',
            'currentVersion' => '5.0.0',
            'targetVersion' => '5.0.1'
        ]);
        $this->assertRedirect('/baser/admin/baser-core/plugins/update');
        $this->assertFlashMessage('アップデート処理が完了しました。画面下部のアップデートログを確認してください。');
        rename(BASER . 'VERSION.bak.txt', BASER . 'VERSION.txt');
        rename(ROOT . DS . 'composer.json.bak', ROOT . DS . 'composer.json');
        rename(ROOT . DS . 'composer.lock.bak', ROOT . DS . 'composer.lock');
        BcComposer::update();
    }

    /**
     * データベースをリセットする
     */
    public function testReset_db()
    {
        $this->markTestIncomplete('このメソッドを利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
        // データが初期化されなくなってしまう。dropTableでトリガーが削除されるのが原因の様子

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

        $folder = new BcFolder(BASER_PLUGINS . $plugin);
        $folder->delete();
        $folder = new BcFolder($zipSrcPath);
        $folder->delete();
    }

    /**
     * test add
     */
    public function test_add_fail()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

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

        $this->setUploadFileToRequest('file', $testFile, '', 1);
        $this->setUnlockedFields(['file']);
        $this->post('/baser/admin/baser-core/plugins/add');
        $this->assertResponseCode(302);
        $this->assertFlashMessage('ファイルのアップロードに失敗しました。Cannot retrieve stream due to upload error: The uploaded file exceeds the upload_max_filesize directive in php.ini');

        $folder = new BcFolder(BASER_PLUGINS . $plugin);
        $folder->delete();
        $folder = new BcFolder($zipSrcPath);
        $folder->delete();
    }

    /**
     * test get_core_update
     */
    public function test_get_core_update()
    {
        $this->markTestIncomplete('CakePHPのバージョンの問題があるので、baserCMS 5.1.0 をリリースしてから再実装する');
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        // composer.json をバックアップ
        copy(ROOT . DS . 'composer.json', ROOT . DS . 'composer.bak.json');
        copy(ROOT . DS . 'composer.lock', ROOT . DS . 'composer.bak.lock');

        // composer.json を配布用に更新
        BcComposer::setup('', ROOT . DS);
        BcComposer::setupComposerForDistribution('5.0.15');

        $this->post('/baser/admin/baser-core/plugins/get_core_update', [
            'targetVersion' => '5.0.15',
            'php' => 'php',
        ]);
        $this->assertResponseCode(302);
        $this->assertFlashMessage('最新版のダウンロードが完了しました。アップデートを実行してください。');
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'plugins',
            'action' => 'update'
        ]);

        // 一時ファイルを削除
        (new BcFolder(TMP . 'update'))->delete();

        // composer.json を元に戻す
        rename(ROOT . DS . 'composer.bak.json', ROOT . DS . 'composer.json');
        rename(ROOT . DS . 'composer.bak.lock', ROOT . DS . 'composer.lock');
    }

}
