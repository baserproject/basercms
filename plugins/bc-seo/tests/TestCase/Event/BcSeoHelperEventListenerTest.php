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

namespace BcSeo\Test\TestCase\Event;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Test\Scenario\InitAppScenario;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use BcSeo\Event\BcSeoHelperEventListener;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\View\Form\EntityContext;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BcSeoHelperEventListenerTest
 */
class BcSeoHelperEventListenerTest extends BcTestCase
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
     * testFormBeforeCreate
     */
    public function testFormBeforeCreate()
    {
        $this->loginAdmin($this->getRequest('/baser/admin/bc-blog/blog_posts/edit/1/1'));
        $listener = new BcSeoHelperEventListener();
        $event = new Event('formBeforeCreate', new View(), ['id' => 'BlogPostForm']);
        $listener->formBeforeCreate($event);
        $options = $event->getData('options');
        // enctype確認
        $this->assertEquals('multipart/form-data', $options['enctype']);
    }

    /**
     * testBcFormTableAfter
     */
    public function testBcFormTableAfter()
    {
        $this->loginAdmin($this->getRequest('/baser/admin/bc-blog/blog_posts/edit/1/1'));
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'blog_category_id' => 1, 'status' => true])
            ->persist();

        $view = new View();
        $view->loadHelper('BaserCore.BcBaser');
        $view->loadHelper('BaserCore.BcUpload');
        $view->loadHelper('BaserCore.BcAdminForm');
        $blogPostsTable = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');
        $view->BcAdminForm->context(new EntityContext([
            'table' => $blogPostsTable,
            'entity' => $blogPostsTable->get(1),
        ]));

        $event = new Event('bcFormTableAfter', $view, ['id' => 'BlogPostForm']);

        ob_start();
        $listener = new BcSeoHelperEventListener();
        $listener->bcFormTableAfter($event);
        $result = ob_get_clean();

        // エレメント確認
        $this->assertStringContainsString('SEO設定', $result);
    }
}
