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

namespace BaserCore\Test\TestCase;

use BaserCore\BcPlugin;
use BaserCore\Service\SitesService;
use BaserCore\Test\Factory\PluginFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class BcPluginTest
 * @package BaserCore\Test\TestCase
 */
class BcPluginTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;

    /**
     * @var BcPlugin
     */
    public $BcPlugin;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->BcPlugin = new BcPlugin(['name' => 'BcBlog']);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcPlugin);
        parent::tearDown();
        $this->truncateTable('blog_categories');
        $this->truncateTable('blog_contents');
        $this->truncateTable('blog_posts');
        $this->truncateTable('blog_tags');
        $this->truncateTable('blog_posts_blog_tags');
    }

    /**
     * testInitialize
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcPlugin->migrations);
    }

    /**
     * testInstall
     */
    public function testInstallAndUninstall()
    {
        // インストール
        $this->BcPlugin->install(['connection' => 'test']);
        $plugins = $this->getTableLocator()->get('BaserCore.Plugins')->find()->where(['name' => 'BcBlog'])->first();
        $this->assertEquals(1, $plugins->priority);

        // アンインストール
        $from = BcUtil::getPluginPath('BcBlog');
        $pluginDir = dirname($from);
        $folder = new Folder();
        $to = $pluginDir . DS . 'BcBlogBak';
        $folder->copy($to, [
            'from' => $from,
            'mode' => 0777
        ]);
        $folder->create($from, 0777);
        $this->BcPlugin->uninstall(['connection' => 'test']);
        $this->assertFalse(is_dir($from));
        $plugins = $this->getTableLocator()->get('BaserCore.Plugins')->find()->where(['name' => 'BcBlog'])->first();
        $this->assertNull($plugins);
        $folder->move($from, [
            'from' => $to,
            'mode' => 0777,
            'schema' => Folder::OVERWRITE
        ]);
        $this->BcPlugin->install(['connection' => 'test']);
    }

    /**
     * testRollback
     */
    public function testRollback()
    {
        $this->BcPlugin->install(['connection' => 'test']);
        $this->BcPlugin->rollbackDb(['connection' => 'test']);
        $collection = ConnectionManager::get('default')->getSchemaCollection();
        $tables = $collection->listTables();
        $this->assertNotContains('blog_posts', $tables);
        $plugins = $this->getTableLocator()->get('BaserCore.Plugins');
        $plugins->deleteAll(['name' => 'BcBlog']);
        $this->BcPlugin->install(['connection' => 'test']);
    }

    /**
     * testRoutes
     */
    public function testRoutes()
    {
        $routes = Router::createRouteBuilder('/');
        $this->BcPlugin->routes($routes);

        // コンテンツ管理のプラグイン用のリバースルーティング
        $this->getRequest('/');
        $this->assertEquals('/news/', Router::url([
            'plugin' => 'BcBlog',
            'controller' => 'Blog',
            'action' => 'index',
            'entityId' => 31
        ]));
        $this->assertEquals('/news/archives/1', Router::url([
            'plugin' => 'BcBlog',
            'controller' => 'Blog',
            'action' => 'archives',
            'entityId' => 31,
            1
        ]));

        // 管理画面のプラグイン用ルーティング
        $result = Router::parseRequest($this->getRequest('/baser/admin/bc-blog/blog_contents/index'));
        $this->assertEquals('BlogContents', $result['controller']);
        $result = Router::parseRequest($this->getRequest('/baser/admin/bc-blog/blog_contents/edit/1'));
        $this->assertEquals('BlogContents', $result['controller']);

        // フロントエンドのプラグイン用ルーティング
        $result = Router::parseRequest($this->getRequest('/bc-blog/blog_contents/index'));
        $this->assertEquals('BlogContents', $result['controller']);
        $result = Router::parseRequest($this->getRequest('/bc-blog/blog_contents/edit/1'));
        $this->assertEquals('BlogContents', $result['controller']);

        // サブサイトのプラグイン用ルーティング
        Router::reload();
        $routes = Router::createRouteBuilder('');
        $_SERVER['REQUEST_URI'] = '/s/';
        $this->BcPlugin->routes($routes);
        $result = Router::parseRequest($this->getRequest('/s/bc-blog/blog_contents/index'));
        $this->assertEquals('BlogContents', $result['controller']);
        $this->assertEquals('s', $result['sitePrefix']);
        $result = Router::parseRequest($this->getRequest('/s/bc-blog/blog_contents/edit/1'));
        $this->assertEquals('BlogContents', $result['controller']);
        $this->assertEquals('s', $result['sitePrefix']);

        // 管理画面のプラグイン用ルーティング
        $result = Router::parseRequest($this->getRequest('/baser/api/bc-blog/blog_contents/index.json'));
        $this->assertEquals('BlogContents', $result['controller']);
        $this->assertEquals('json', $result['_ext']);
        $result = Router::parseRequest($this->getRequest('/baser/api/bc-blog/blog_contents/edit/1.json'));
        $this->assertEquals('BlogContents', $result['controller']);
        $this->assertEquals('json', $result['_ext']);
        unset($_SERVER['REQUEST_URI']);
    }

    /**
     * test getUpdateScriptMessages And getUpdaters
     */
    public function test_getUpdateScriptMessagesAndGetUpdaters()
    {
        $name = 'Sample';
        $pluginPath = ROOT . DS . 'plugins' . DS . $name . DS;
        $updatePath = $pluginPath . 'config' . DS . 'update' . DS;
        PluginFactory::make(['name' => $name, 'title' => 'サンプル', 'version' => '1.0.0'])->persist();
        $folder = new Folder();

        // 新バージョン
        $folder->create($pluginPath);
        $file = new File($pluginPath . 'VERSION.txt');
        $file->write('1.0.3');
        $file->close();
        // アップデートスクリプト 0.0.1
        $folder->create($updatePath . '0.0.1');
        $file = new File($updatePath . '0.0.1' . DS . 'config.php');
        $file->write('<?php return [\'updateMessage\' => \'test0\'];');
        $file->close();
        // アップデートスクリプト 1.0.1
        $folder->create($updatePath . '1.0.1');
        $file = new File($updatePath . '1.0.1' . DS . 'config.php');
        $file->write('<?php return [\'updateMessage\' => \'test1\'];');
        $file->close();
        $file = new File($updatePath . '1.0.1' . DS . 'updater.php');
        $file->create();
        $file->close();
        // アップデートスクリプト 1.0.2
        $folder->create($updatePath . '1.0.2');
        $file = new File($updatePath . '1.0.2' . DS . 'config.php');
        $file->write('<?php return [\'updateMessage\' => \'test2\'];');
        $file->close();
        $file = new File($updatePath . '1.0.2' . DS . 'updater.php');
        $file->create();
        $file->close();
        // アップデートスクリプト 1.0.4
        $folder->create($updatePath . '1.0.4');
        $file = new File($updatePath . '1.0.4' . DS . 'config.php');
        $file->write('<?php return [\'updateMessage\' => \'test3\'];');
        $file->close();

        $this->assertEquals(
            ['Sample-1.0.1' => 'test1', 'Sample-1.0.2' => 'test2'],
            $this->BcPlugin->getUpdateScriptMessages($name)
        );
        $this->assertEquals(
            ['Sample-1.0.1' => 1000001000, 'Sample-1.0.2' => 1000002000],
            $this->BcPlugin->getUpdaters($name)
        );
        $folder->delete($pluginPath);
    }

    /**
     * test execScript
     */
    public function test_execScript()
    {
        $this->fixtureStrategy->teardownTest();
        $version = '1.0.0';
        $updatePath = Plugin::path('BcBlog') . 'config' . DS . 'update';
        $versionPath = $updatePath . DS . $version;
        // スクリプトなし
        if(file_exists($versionPath . DS . 'updater.php')) {
            unlink($versionPath . DS . 'updater.php');
        }
        $this->assertTrue($this->BcPlugin->execScript($version));
        // 有効スクリプトあり
        UserFactory::make(['id' => 1, 'name' => 'test'])->persist();
        $folder = new Folder();
        $folder->create($versionPath);
        $file = new File($versionPath . DS . 'updater.php');
        $file->write('<?php
use Cake\ORM\TableRegistry;
$users = TableRegistry::getTableLocator()->get(\'BaserCore.Users\');
$user = $users->find()->where([\'id\' => 1])->first();
$user->name = \'hoge\';
$users->save($user);');
        $file->close();
        $this->BcPlugin->execScript($version);
        $users = $this->getTableLocator()->get('BaserCore.Users');
        $user = $users->find()->where(['id' => 1])->first();
        $this->assertEquals('hoge', $user->name);
        // 無効スクリプトあり
        $file = new File($versionPath . DS . 'updater.php');
        $file->write('<?php
$this->log(\'test\');');
        $file->close();
        $this->BcPlugin->execScript($version);
        $file = new File(LOGS . 'cli-error.log');
        $log = $file->read();
        $this->assertStringContainsString('test', $log);
        // 初期化
        $folder->delete($updatePath);
    }

    /**
     * test createAssetsSymlink
     */
    public function test_createAssetsSymlink()
    {
        unlink(WWW_ROOT . 'baser_core');
        $this->BcPlugin->createAssetsSymlink();
        $this->assertTrue(file_exists(WWW_ROOT . 'baser_core'));
    }

    /**
     * test migrate
     */
    public function test_migrate()
    {
        $pluginPath = ROOT . DS . 'plugins' . DS . 'BcTest' . DS;
        $folder = new Folder();

        // プラグインフォルダを初期化
        $folder->delete($pluginPath);
        $configPath = $pluginPath . 'config' . DS;
        $migrationPath = $configPath . 'Migrations' . DS;
        $seedPath = $configPath . 'Seeds' . DS;
        $srcPath = $pluginPath . 'src' . DS;
        $folder->create($srcPath);
        $folder->create($migrationPath);
        $folder->create($seedPath);

        // VERSION.txt
        $this->createVersionFile($pluginPath, '0.0.1');

        // src/Plugin.php
        $this->createPluginFile($srcPath);

        // config/Migrations/20220626000000_InitialBcTest.php
        $this->createInitialMigrationFile($migrationPath);

        // インストール実行
        $plugin = new BcPlugin(['name' => 'BcTest']);
        $plugin->install(['connection' => 'test']);
        $db = ConnectionManager::get('test');
        $collection = $db->getSchemaCollection();
        $tableSchema = $collection->describe('bc_test');
        $this->assertEquals('string', $tableSchema->getColumnType('name'));

        // config/Migrations/20220627000000_AlterBcTest.php
        $this->createAlterMigrationFile($migrationPath);

        // アップデート実行
        // インストールで利用した BcPluginを使い回すと、マイグレーションのキャッシュが残っていて、
        // 新しいマイグレーションファイルを認識しないので初期化しなおす
        $plugin = new BcPlugin(['name' => 'BcTest']);
        $plugin->migrate(['connection' => 'test']);
        $tableSchema = $collection->describe('bc_test');
        $this->assertEquals('datetime', $tableSchema->getColumnType('name'));

        // 初期化
        $folder->delete($pluginPath);
        $this->dropTable('bc_test');
        $this->dropTable('bc_test_phinxlog');
    }

    /**
     * プラグインファイルを作成する
     *
     * @param $srcPath
     */
    public function createPluginFile($srcPath)
    {
        $file = new File($srcPath . 'Plugin.php');
                $file->write('<?php
        namespace BcTest;
        use BaserCore\BcPlugin;
        class Plugin extends BcPlugin {}');
    }

    /**
     * Alter のマイグレーションファイルを作成する
     *
     * @param $migrationPath
     */
    public function createAlterMigrationFile($migrationPath)
    {
        $file = new File($migrationPath . '20220627000000_AlterBcTest.php', 'w');
        $file->write('<?php
use Migrations\AbstractMigration;
class AlterBcTest extends AbstractMigration
{
    public function change()
    {
        $table = $this->table(\'bc_test\');
        $table->changeColumn(\'name\', \'datetime\');
        $table->update();
    }
}');
    }

    /**
     * 初期化用のマイグレーションファイルを作成する
     *
     * @param $migrationPath
     */
    public function createInitialMigrationFile($migrationPath)
    {
        $file = new File($migrationPath . '20220626000000_InitialBcTest.php', 'w');
        $file->write('<?php
use Migrations\AbstractMigration;
class InitialBcTest extends AbstractMigration
{
    public function up()
    {
        $this->table(\'bc_test\')
            ->addColumn(\'name\', \'string\', [
                \'default\' => null,
                \'limit\' => 255,
                \'null\' => true,
            ])
            ->create();
    }
    public function down()
    {
        $this->table(\'bc_test\')->drop()->save();
    }
}');
    }

    /**
     * バージョンファイルを作成する
     *
     * @param $pluginPath
     * @param $version
     */
    public function createVersionFile($pluginPath, $version)
    {
        $file = new File($pluginPath . 'VERSION.txt');
        $file->write($version);
    }

    /**
     * アップデーターを作成する
     *
     * @param $updaterPath
     */
    public function createUpdater($updaterPath)
    {
        $file = new File($updaterPath . 'updater.php', 'w');
        $file->write('<?php
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
$table = TableRegistry::getTableLocator()->get(\'BcTest.BcTest\');
$table->save(new Entity([\'name\' => \'2022-06-26\']));');
    }

    /**
     * test execUpdater
     */
    public function test_execUpdater()
    {
        $pluginPath = ROOT . DS . 'plugins' . DS . 'BcTest' . DS;
        $folder = new Folder();

        // プラグインフォルダを初期化
        $folder->delete($pluginPath);
        $configPath = $pluginPath . 'config' . DS;
        $migrationPath = $configPath . 'Migrations' . DS;
        $seedPath = $configPath . 'Seeds' . DS;
        $srcPath = $pluginPath . 'src' . DS;
        $folder->create($srcPath);
        $folder->create($migrationPath);
        $folder->create($seedPath);

        // VERSION.txt
        $this->createVersionFile($pluginPath, '0.0.1');

        // config/Migrations/20220626000000_InitialBcTest.php
        $this->createInitialMigrationFile($migrationPath);

        // src/Plugin.php
        $this->createPluginFile($srcPath);

        // インストール実行
        $plugin = new BcPlugin(['name' => 'BcTest']);
        $plugin->install(['connection' => 'test']);

        // VERSION.txt
        $this->createVersionFile($pluginPath, '0.0.2');

        // config/update/0.0.2/updater.php
        $updaterPath = $configPath . 'update' . DS . '0.0.2' . DS;
        $folder->create($updaterPath);
        $this->createUpdater($updaterPath);

        // アップデート実行
        $plugin->execUpdater();
        $table = $this->getTableLocator()->get('BcTest.BcTest');
        $entity = $table->find()->first();
        $this->assertEquals('2022-06-26', (string) $entity->name);

        // 初期化
        $folder->delete($pluginPath);
        $this->dropTable('bc_test');
        $this->dropTable('bc_test_phinxlog');
    }

    /**
     * テーマを適用する
     */
    public function test_applyAsTheme()
    {
        $targetId = 1;
        $currentTheme = 'BcFront';
        $SiteService = new SitesService();
        $site = $SiteService->get($targetId);
        $this->assertEquals($currentTheme, $site->theme);

        $updateTheme = 'BcSpaSample';
        $this->BcPlugin->applyAsTheme($site, $updateTheme);
        $site = $SiteService->get($targetId);
        $this->assertEquals($updateTheme, $site->theme);
    }

    /**
     * test Rest API
     */
    public function testRestApi()
    {
        Router::resetRoutes();
        // 件数確認
        $this->get('/baser/api/baser-core/pages.json');
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(0, count($result->pages));

        // 一件追加
        $token = $this->apiLoginAdmin();
        $this->post('/baser/api/baser-core/pages.json?token=' . $token['access_token'], [
            'content' => [
                'parent_id' => 1,
                'title' => 'sample',
                'plugin' => 'BaserCore',
                'type' => 'Page',
                'site_id' => 1,
                'alias_id' => '',
                'entity_id' => '',
            ],
            'contents' => '',
            'draft' => '',
            'page_template' => '',
            'code' => ''
        ]);
        $result = json_decode((string)$this->_response->getBody());
        $id = $result->page->id;

        // 件数確認（認証済）
        $this->get('/baser/api/baser-core/pages.json?token=' . $token['access_token'] . '&' . 'status=');
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(1, count($result->pages));

        // 変更（公開状態に変更）
        $this->put('/baser/api/baser-core/pages/' . $id . '.json?token=' . $token['access_token'], [
            'content' => [
                'self_status' => 1
            ]
        ]);

        // 件数確認（認証なし）
        $this->get('/baser/api/baser-core/pages.json');
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(1, count($result->pages));

        // 削除
        $this->delete('/baser/api/baser-core/pages/' . $id . '.json?token=' . $token['access_token']);

        // 件数確認（認証済）
        $this->get('/baser/api/baser-core/pages.json?token=' . $token['access_token'] . '&' . 'status=');
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(0, count($result->pages));
    }

}
