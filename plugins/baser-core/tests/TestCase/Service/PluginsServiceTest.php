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
    protected $fixtures = [
        'plugin.BaserCore.Plugins',
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
            ["1", 'BcBlog', "3"],
        ];
    }

    /**
     * testGetPluginConfig
     */
    public function testGetPluginConfig()
    {
        $plugin = $this->Plugins->getPluginConfig('BaserCore');
        $this->assertEquals('BaserCore', $plugin->name);
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
        $this->Plugins->install('BcBlog');
        $blogPosts = $this->getTableLocator()->get('BcBlog.BlogPosts');
        $blogPosts->save($blogPosts->newEntity([
            'name' => 'test'
        ]));
        $this->Plugins->resetDb('BcBlog', ['connection' => 'test']);
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

}
