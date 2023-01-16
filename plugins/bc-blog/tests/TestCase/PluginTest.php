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

use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use BcBlog\Service\Admin\BlogCategoriesAdminServiceInterface;
use BcBlog\Service\Admin\BlogContentsAdminServiceInterface;
use BcBlog\Service\BlogCategoriesServiceInterface;
use BcBlog\Service\BlogContentsServiceInterface;
use BcBlog\Service\Front\BlogFrontServiceInterface;
use Cake\Core\Container;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;

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
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BcBlog.Factory/BlogTags',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcBlog.Factory/BlogContents',
        'plugin.BcBlog.Factory/BlogCategories',
        'plugin.BcBlog.Factory/BlogPostsBlogTags',
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

    /**
     * test service
     */
    public function testService()
    {
        $container = new Container();
        $this->Plugin->services($container);
        $this->assertTrue($container->has(BlogCategoriesServiceInterface::class));
        $this->assertTrue($container->has(BlogCategoriesAdminServiceInterface::class));
        $this->assertTrue($container->has(BlogContentsServiceInterface::class));
        $this->assertTrue($container->has(BlogContentsAdminServiceInterface::class));
        $this->assertTrue($container->has(BlogFrontServiceInterface::class));
    }

    /**
     * test routes
     */
    public function testRoutes() {
        $routes = Router::createRouteBuilder('/');
        $this->Plugin->routes($routes);

        // TODO ucmitz 未実装
//        $result = Router::parseRequest($this->getRequest('/rss/index'));
//        $this->assertEquals('blog', $result['controller']);

        $result = Router::parseRequest($this->getRequest('/tags/test'));
        $this->assertEquals('tags', $result['action']);

        SiteFactory::make(['alias' => 'as', 'name' => 'LoremIpsum'])->persist();
        Router::setRequest(new ServerRequest(['url' => '/as/']));
        $this->Plugin->routes($routes);
        $result = Router::parseRequest($this->getRequest('/as/tags/bla'));
        $this->assertEquals('LoremIpsum', $result['sitePrefix']);
    }
}
