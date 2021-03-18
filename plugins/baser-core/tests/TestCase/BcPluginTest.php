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
use Cake\Datasource\ConnectionManager;

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
     * testRoutes
     */
    public function testRoutes()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * testInstall
     */
    public function testInstall()
    {
        $this->BcPlugin->install(['connection' => 'test']);
        $plugins = $this->getTableLocator()->get('Plugins')->find()->where(['name' => 'BcBlog'])->first();
        $this->assertEquals(1, $plugins->priority);
        // インストーラーで追加したテーブルを削除
        $connection = ConnectionManager::get('test');
        $this->BcPlugin->migrations->rollback(['plugin' => 'BcBlog', 'connection' => 'test']);
        // bc_blog_phinxlog 削除
        $schema = $connection->getDriver()->newTableSchema('bc_blog_phinxlog');
        $sql = $schema->dropSql($connection);
        $connection->execute($sql[0])->closeCursor();
    }

    /**
     * testUninstall
     */
    public function testUninstall()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
