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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\PluginsService;
use BaserCore\Test\Factory\PluginFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Core\App;
use Cake\ORM\TableRegistry;

/**
 * Class PluginsServiceTest
 * @package BaserCore\Test\TestCase\Service
 * @property PluginsService $Plugins
 */
class PluginsServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.SiteConfigs'
    ];

    /**
     * @var PluginsService|null
     */
    public $Plugins = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->Plugins = new PluginsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Plugins);
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->Plugins->Plugins));
    }

    /**
     * sortModeが1の時、DBに登録されてるプラグインのみ取得
     * sortModeが0の時、DBに登録されてるプラグインとプラグインファイル全て取得
     * @param string $sortMode ソートモードかどうか ｜ DBに登録されてるデータかファイルも含めるか
     * @param string $expectedPlugin 期待されるプラグイン
     * @param string $expectedCount 期待される取得数
     * @return void
     * Test getIndex
     * @dataProvider indexDataprovider
     */
    public function testGetIndex($sortMode, $expectedPlugin): void
    {
        // テスト用のプラグインフォルダ作成
        $pluginPath = App::path('plugins')[0] . DS . 'BcTest';
        $folder = new Folder($pluginPath);
        $folder->create($pluginPath, 0777);

        $plugins = $this->Plugins->getIndex($sortMode);
        $pluginNames = [];
        foreach($plugins as $plugin) {
            $pluginNames[] = $plugin->name;
        }
        //期待されるプラグインを含むか
        $this->assertContains($expectedPlugin, $pluginNames);
        $folder->delete($pluginPath);
        if ($sortMode) {
            // フォルダ内プラグインが含まれてないか
            $this->assertNotContains('BcTest', $pluginNames);
        }
    }
    public function indexDataprovider()
    {
        return [
            // 普通の場合 | DBに登録されてるプラグインとプラグインファイル全て
            ["0", 'BcTest'],
            // ソートモードの場合 | DBに登録されてるプラグインのみ
            ["1", 'BcBlog'],
        ];
    }

    /**
     * test install
     */
    public function testInstall()
    {
        // 正常な場合
        $this->assertTrue($this->Plugins->install('BcUploader', 'test'));
        // プラグインがない場合
        try {
            $this->Plugins->install('UnKnown', 'test');
        } catch (\Exception $e) {
            $this->assertEquals("Plugin UnKnown could not be found.", $e->getMessage());
        }
        // フォルダはあるがインストールできない場合
        $pluginPath = App::path('plugins')[0] . DS . 'BcTest';
        $folder = new Folder($pluginPath);
        $folder->create($pluginPath, 0777);
        try {
            $this->assertNull($this->Plugins->install('BcTest', 'test'));
        } catch (\Exception $e) {
            $this->assertEquals("プラグインに Plugin クラスが存在しません。src ディレクトリ配下に作成してください。", $e->getMessage());
        }
        $folder->delete($pluginPath);
    }

    /**
     * test getByName
     */
    public function testGetByName()
    {
        $this->assertEquals('BcBlog', $this->Plugins->getByName('BcBlog')->name);
        $this->assertNull($this->Plugins->getByName('Test'));
    }
    /**
     * test resetDb
     * @throws \Exception
     */
    public function testResetDb()
    {
        $this->Plugins->install('BcBlog', 'test');
        $blogPosts = $this->getTableLocator()->get('BcBlog.plugins');

        $rs = $blogPosts->find()->where(['name' => 'BcBlog'])->first();
        $this->assertTrue($rs->db_init);

        $this->Plugins->resetDb('BcBlog', 'test');

        $rs = $blogPosts->find()->where(['name' => 'BcBlog'])->first();
        $this->assertFalse($rs->db_init);
    }

    /**
     * test uninstall
     */
    public function testUninstall()
    {
        // TODO インストールの処理とまとめる予定
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test get
     */
    public function testGet()
    {
        $plugin = $this->Plugins->get(1);
        $this->assertEquals('BcBlog', $plugin->name);
    }

    /**
     * アクセス制限設定を追加する
     */
    public function testAllow()
    {
        $data = [
            'name' => 'BcTest',
            'title' => 'テスト',
            'status' => "0",
            'version' => "1.0.0",
            'permission' => "1"
        ];

        $this->Plugins->allow($data);
        $permissions = TableRegistry::getTableLocator()->get('BaserCore.Permissions');
        $result = $permissions->find('all')->all();

        $this->assertEquals($data['title'] ." 管理", $result->last()->name);
    }

    /**
     * test getInstallStatusMessage
     */
    public function testGetInstallStatusMessage()
    {
        $this->assertEquals('既にインストール済のプラグインです。', $this->Plugins->getInstallStatusMessage('BcBlog'));
        $this->assertEquals('インストールしようとしているプラグインのフォルダが存在しません。', $this->Plugins->getInstallStatusMessage('BcTest'));
        $pluginPath = App::path('plugins')[0] . DS . 'BcTest';
        $folder = new Folder($pluginPath);
        $folder->create($pluginPath, 0777);
        $this->assertEquals('', $this->Plugins->getInstallStatusMessage('BcTest'));
        $folder->delete($pluginPath);
    }

    /**
     * test getVersion
     */
    public function test_getVersion()
    {
        $this->assertEquals('', $this->Plugins->getVersion('Hoge'));
        PluginFactory::make(['name' => 'Hoge', 'version' => '2.0.0'])->persist();
        $this->assertEquals('2.0.0', $this->Plugins->getVersion('Hoge'));
    }

    /**
     * test update
     * @throws \Exception
     */
    public function test_update()
    {
        // プラグイン
        $this->Plugins->install('BcSpaSample', 'test');
        $pluginPath = Plugin::path('BcSpaSample');
        rename($pluginPath . 'VERSION.txt', $pluginPath . 'VERSION.bak.txt');
        $file = new File($pluginPath . 'VERSION.txt');
        $file->write('10.0.0');
        $this->Plugins->update('BcSpaSample', 'test');
        $this->assertEquals('10.0.0', $this->Plugins->getVersion('BcSpaSample'));
        rename($pluginPath . 'VERSION.bak.txt', $pluginPath . 'VERSION.txt');

        // コア
        rename(BASER . 'VERSION.txt', BASER . 'VERSION.bak.txt');
        $file = new File(BASER . 'VERSION.txt');
        $file->write('10.0.0');
        $this->Plugins->update('BaserCore', 'test');
        $plugins = array_merge(['BaserCore'], Configure::read('BcApp.corePlugins'));
        foreach($plugins as $plugin) {
            $this->assertEquals('10.0.0', BcUtil::getVersion($plugin));
        }
        rename(BASER . 'VERSION.bak.txt', BASER . 'VERSION.txt');
    }

    /**
     * test detachAll
     */
    public function test_detachAll()
    {
        $result = $this->Plugins->detachAll();
        $this->assertEquals(4, count($result));
    }

    /**
     * test attachAllFromIds
     * @return void
     */
    public function test_attachAllFromIds(){
        $plugins = $this->Plugins->getIndex(false);
        $this->assertTrue($plugins[1]->status);

        $ids = [1,2];

        $this->Plugins->detachAll();

        $this->Plugins->attachAllFromIds($ids);
        $plugin = $this->Plugins->get(1);
        $this->assertTrue($plugin->status);

        $plugin = $this->Plugins->get(2);
        $this->assertTrue($plugin->status);
    }

    /**
     * test attachAllFromIds with 配列：null
     * @return void
     */
    public function test_attachAllFromIds_false(){
        $ids = null;
        $rs = $this->Plugins->attachAllFromIds($ids);

        $this->assertNull($rs);
    }

    /**
     * test getMarketPlugins
     * @return void
     */
    public function testGetMarketPlugins(){
        $rs = $this->Plugins->getMarketPlugins();
        $this->assertNotEmpty($rs);
        $caches = Cache::read('baserMarketPlugins', '_bc_env_');
        $this->assertIsArray($caches);
    }

    /**
     * test getNamesById
     * @return void
     */
    public function testGetNamesById()
    {
        $rs = $this->Plugins->getNamesById([1, 2, 3]);

        $this->assertEquals('ブログ', $rs[1]);
        $this->assertEquals('メール', $rs[2]);
        $this->assertEquals('アップローダー', $rs[3]);
    }

    /**
     * test batch
     * @return void
     */
    public function testBatch()
    {
        PluginFactory::make(['id' => 10, 'name' => 'plugin 1', 'status' => 1])->persist();
        PluginFactory::make(['id' => 11, 'name' => 'plugin 2', 'status' => 1])->persist();
        PluginFactory::make(['id' => 12, 'name' => 'plugin 3', 'status' => 1])->persist();

        $this->Plugins->batch('detach', [10, 11, 12]);

        $this->assertFalse($this->Plugins->get(10)->status);
        $this->assertFalse($this->Plugins->get(11)->status);
        $this->assertFalse($this->Plugins->get(12)->status);
    }
}
