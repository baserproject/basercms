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

namespace BcBlog\Test\TestCase;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;

/**
 * Class BcPluginTest
 * @package BaserCore\Test\TestCase
 */
class PluginTest extends BcTestCase
{
    /**
     * @var \Cake\Core\PluginInterface
     */
    public $Plugin;

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
        $this->setFixtureTruncate();
        parent::setUp();
        BcUtil::includePluginClass('BcBlog');
        $plugins = Plugin::getCollection();
        $this->Plugin = $plugins->create('BcBlog');
        $plugins->add($this->Plugin);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Plugin);
        parent::tearDown();
    }

    /**
     * testInstall
     */
    public function testInstall()
    {
        // blog_posts / bc_blog_phinxlog テーブルを削除
        $connection = ConnectionManager::get('test');
        $schema = $connection->getDriver()->newTableSchema('blog_posts');
        $sql = $schema->dropSql($connection);
        $connection->execute($sql[0])->closeCursor();
        $schema = $connection->getDriver()->newTableSchema('bc_blog_phinxlog');
        $sql = $schema->dropSql($connection);
        $connection->execute($sql[0])->closeCursor();

        // plugins テーブルより blog_posts を削除
        $plugins = $this->getTableLocator()->get('BaserCore.Plugins');
        $plugins->deleteAll(['name' => 'BcBlog']);

        $expected = ['blog_posts'];
        $this->Plugin->install(['connection' => 'test']);
        // インストールされたテーブルをチェック
        $tables = $connection->getSchemaCollection()->listTables();
        foreach($expected as $value) {
            $this->assertContains($value, $tables);
        }
        // インストーラーで追加したテーブルを削除
        $this->Plugin->migrations->rollback(['plugin' => 'BcBlog', 'connection' => 'test']);
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
