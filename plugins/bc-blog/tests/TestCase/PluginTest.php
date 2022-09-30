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
        // テーブルを削除
        $this->dropTable('blog_posts');
        $this->dropTable('blog_categories');
        $this->dropTable('blog_contents');
        $this->dropTable('blog_comments');
        $this->dropTable('blog_tags');
        $this->dropTable('blog_posts_blog_tags');
        $this->dropTable('bc_blog_phinxlog');

        // plugins テーブルより blog_posts を削除
        $plugins = $this->getTableLocator()->get('BaserCore.Plugins');
        $plugins->deleteAll(['name' => 'BcBlog']);

        $expected = ['blog_posts'];
        $this->Plugin->install(['connection' => 'test']);
        // インストールされたテーブルをチェック
        $connection = ConnectionManager::get('test');
        $tables = $connection->getSchemaCollection()->listTables();
        foreach($expected as $value) {
            $this->assertContains($value, $tables);
        }
    }

    /**
     * testUninstall
     */
    public function testUninstall()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
