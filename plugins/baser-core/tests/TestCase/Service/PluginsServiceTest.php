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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\PluginsService;
use BaserCore\TestSuite\BcTestCase;
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
        'plugin.BaserCore.UserGroups'
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
     * sortModeが1の時、DBに登録されてるプラグインのみ取得
     * sortModeが0の時、DBに登録されてるプラグインとプラグインファイル全て取得
     * @param string $sortMode ソートモードかどうか ｜ DBに登録されてるデータかファイルも含めるか
     * @param string $expectedPlugin 期待されるプラグイン
     * @param string $expectedCount 期待される取得数
     * @return void
     * Test getIndex
     * @dataProvider indexDataprovider
     */
    public function testGetIndex($sortMode, $expectedPlugin, $expectedCount): void
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
        $folder->delete($pluginPath);
        if ($sortMode) {
            // フォルダ内プラグインが含まれてないか
            $this->assertNotContains('BcTest', $pluginNames);
        }
        //期待されるプラグインを含むか
        $this->assertContains($expectedPlugin, $pluginNames);
        // プラグイン数
        $this->assertEquals(count($plugins), $expectedCount);
    }
    public function indexDataprovider()
    {
        return [
            // 普通の場合 | DBに登録されてるプラグインとプラグインファイル全て
            ["0", 'BcTest', "5"],
            // ソートモードの場合 | DBに登録されてるプラグインのみ
            ["1", 'BcBlog', "2"],
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
            $this->assertNull($this->Plugins->install('BcTest', ['connection' => 'test']));
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
        $this->markTestIncomplete('テストが未実装です');
        // TODO インストールが実装できしだい
        $this->Plugins->install('BcBlog', 'test');
        $blogPosts = $this->getTableLocator()->get('BcBlog.BlogPosts');
        $blogPosts->save($blogPosts->newEntity([
            'name' => 'test'
        ]));
        $this->Plugins->resetDb('BcBlog', 'test');
        $this->assertEquals(0, $blogPosts->find()->where(['name' => 'test'])->count());
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

}
