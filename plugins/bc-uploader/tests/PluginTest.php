<?php
// TODO ucmitz  : コード確認要
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

//namespace BcBlog\Test\TestCase;
//
//use BaserCore\TestSuite\BcTestCase;
//use BaserCore\Utility\BcUtil;
//use Cake\Core\Plugin;
//use Cake\Datasource\ConnectionManager;

/**
 * Class BcPluginTest
 * @package BaserCore\Test\TestCase
 */
class BcPluginTest extends BcTestCase
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
        $expected = ['blog_posts'];
        $this->Plugin->install(['connection' => 'test']);
        // インストールされたテーブルをチェック
        $connection = ConnectionManager::get('test');
        $tables = $connection->getSchemaCollection()->listTables();
        foreach($expected as $value) {
            $this->assertContains($value, $tables);
        }
        // インストーラーで追加したテーブルを削除
        $connection = ConnectionManager::get('test');
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
