<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSeo\Test\TestCase\View\Helper;

use BaserCore\Routing\Route\BcContentsRoute;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BcBlog\Test\Factory\BlogPostFactory;
use BcSeo\Test\Factory\SeoMetaFactory;
use BcSeo\View\Helper\SeoHelper;
use Cake\ORM\TableRegistry;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * SeoHelperTest
 */
class SeoHelperTest extends BcTestCase
{
    use ScenarioAwareTrait;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
    }

    /**
     * testInitialize
     */
    public function testInitialize()
    {
        $view = new View();
        $seoHelper = new SeoHelper($view);
        $seoHelper->initialize([]);
        $this->assertFalse($view->get('canonicalUrl'));
    }

    /**
     * testMeta
     */
    public function testMeta()
    {
        $request = $this->getRequest('/');
        $view = new View($request);
        $view->loadHelper('BaserCore.BcBaser');
        $view->loadHelper('BaserCore.BcHtml');
        $seoHelper = new SeoHelper($view);

        $view->set('description', 'test description');
        $view->set('keywords', 'test keywords');

        ob_start();
        $seoHelper->meta();
        $result = ob_get_clean();
        $this->assertEquals("<meta name=\"description\" content=\"test description\">\n<meta name=\"keywords\" content=\"test keywords\">\n", $result);

        ob_start();
        $seoHelper->meta(['description']);
        $result = ob_get_clean();
        $this->assertEquals("<meta name=\"description\" content=\"test description\">\n", $result);
    }

    /**
     * testGetMeta
     */
    public function testGetMeta()
    {
        $view = new View();
        $seoHelper = new SeoHelper($view);

        $view->set('description', 'test description');
        $view->set('keywords', 'test keywords');
        $meta = $seoHelper->getMeta();
        $this->assertEquals('test description', $meta['description']['value']);
        $this->assertEquals('test keywords', $meta['keywords']['value']);
    }

    /**
     * testGetMetaBlogPost
     */
    public function testGetMetaBlogPost()
    {
        ContentFactory::make([
            'id' => 1,
            'site_id' => 1,
            'url' => '/test'
        ])->persist();
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $content = $contentsTable->get(1, contain: ['Sites']);
        $blogPost = BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'title' => 'Test Post',
        ])->persist();

        SeoMetaFactory::make([
            'table_alias' => 'Sites',
            'entity_id' => 1,
            'description' => 'site description',
            'keywords' => 'site keywords',
        ])->persist();
        SeoMetaFactory::make([
            'table_alias' => 'Contents',
            'entity_id' => 1,
            'description' => 'blogContent description',
            'og_type' => 'blogContent og_type',
        ])->persist();
        SeoMetaFactory::make([
            'table_alias' => 'BlogPosts',
            'entity_id' => 1,
            'description' => 'blogPost description',
        ])->persist();

        $route = new BcContentsRoute('/test', []);
        $request = $this->getRequest('/test/archives/1');
        $request = $request->withAttribute('route', $route);
        $request = $request->withAttribute('currentContent', $content);
        $request = $request->withParam('plugin', 'BcBlog');
        $request = $request->withParam('controller', 'Blog');
        $request = $request->withParam('action', 'archives');
        $view = new View($request);
        $view->set('post', $blogPost);

        $seoHelper = new SeoHelper($view);
        $meta = $seoHelper->getMeta();
        $this->assertEquals('blogPost description', $meta['description']['value']);
        $this->assertEquals('site keywords', $meta['keywords']['value']);
        $this->assertEquals('blogContent og_type', $meta['og_type']['value']);
    }
}
