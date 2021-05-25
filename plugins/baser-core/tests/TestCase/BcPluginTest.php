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

namespace BaserCore\Test\TestCase;

use BaserCore\BcPlugin;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use Cake\Routing\RouteBuilder;
use Cake\Routing\RouteCollection;

/**
 * Class BcPluginTest
 * @package BaserCore\Test\TestCase
 * @property BcPlugin $BcPlugin
 */
class BcPluginTest extends BcTestCase
{

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
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
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
        $plugins = $this->getTableLocator()->get('Plugins')->find()->where(['name' => 'BcBlog'])->first();
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
        $plugins = $this->getTableLocator()->get('Plugins')->find()->where(['name' => 'BcBlog'])->first();
        $this->assertNull($plugins);
        $folder->move($from, [
            'from' => $to,
            'mode' => 0777,
            'schema' => Folder::OVERWRITE
        ]);

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
    }

    /**
     * testRoutes
     */
    public function testRoutes()
    {
        $collection = new RouteCollection();
        $routes = new RouteBuilder($collection, '/');
        $this->BcPlugin->routes($routes);
        $all = $collection->routes();
        // connect・fallbacksにより3つコネクションあり|拡張子jsonあり
        $this->assertEquals($all[0]->defaults, ['plugin' => 'BcBlog', 'action' => 'index']);
        $this->assertEquals($all[0]->getExtensions()[0], "json");
        // connect・fallbacksにより3つコネクションあり|拡張子jsonあり
        $this->assertEquals($all[3]->defaults, ['plugin' => 'BcBlog', 'action' => 'index', 'prefix' => 'Api']);
        $this->assertEquals($all[3]->getExtensions()[0], "json");
        // connect・fallbacksにより3つコネクションあり|拡張子jsonなし
        $this->assertEquals($all[6]->defaults, ['plugin' => 'BcBlog', 'action' => 'index', 'prefix' => 'Admin']);
        $this->assertEmpty($all[6]->getExtensions());
        // connectにより1つコネクションあり|拡張子jsonなし
        $this->assertEquals($all[9]->defaults, ['plugin' => 'BaserCore', 'controller' => 'Dashboard', 'action' => 'index', 'prefix' => 'Admin']);
        $this->assertEmpty($all[9]->getExtensions());
    }
}
