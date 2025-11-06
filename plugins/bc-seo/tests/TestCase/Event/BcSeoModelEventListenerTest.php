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

use ArrayObject;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Test\Scenario\InitAppScenario;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use BcSeo\Event\BcSeoModelEventListener;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BcSeoModelEventListenerTest
 */
class BcSeoModelEventListenerTest extends BcTestCase
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
     * testBeforeFind
     */
    public function testBeforeFind()
    {
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'blog_category_id' => 1, 'status' => true])
            ->persist();
        $this->loginAdmin($this->getRequest('/baser/admin/bc-blog/blog_posts/edit/1/1'));
        $blogPostsTable = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');

        $query = $blogPostsTable->find();
        $listener = new BcSeoModelEventListener();
        $listener->beforeFind(new Event('beforeFind', $blogPostsTable), $query);
        // contain確認
        $this->assertArrayHasKey('SeoMetas', $query->getContain());
    }

    /**
     * testBeforeMarshal
     */
    public function testBeforeMarshal()
    {
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'blog_category_id' => 1, 'status' => true])
            ->persist();
        $this->loginAdmin($this->getRequest('/baser/admin/bc-blog/blog_posts/edit/1/1'));
        $blogPostsTable = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');

        $event = new Event('beforeMarshal', $blogPostsTable, ['options' => []]);
        $listener = new BcSeoModelEventListener();
        $listener->beforeMarshal($event, new ArrayObject());
        $options = $event->getData('options');
        // associated確認
        $this->assertContains('SeoMetas', $options['associated']);
    }
}
