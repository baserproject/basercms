<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\Event;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BcBlog\Event\BcBlogViewEventListener;
use BcBlog\Test\Factory\BlogContentFactory;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogCategoryTest
 * @property BcBlogViewEventListener $Listener
 */
class BcBlogViewEventListenerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Listener = new BcBlogViewEventListener();
        $this->loadFixtureScenario(InitAppScenario::class);

    }

    /**
     * Tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test beforeRender
     *
     * @return void
     */
    public function testBeforeRender(): void
    {
        BlogContentFactory::make(['id' => '1'])->persist();
        ContentFactory::make(['id' => 1, 'type' => 'BlogContent', 'entity_id' => 1, 'status' => true ])->persist();

        $this->Listener->beforeRender(new Event('beforeRender', new View()));
        $this->assertArrayNotHasKey('BlogContent1', Configure::read('BcApp.adminNavigation.Contents'));

        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->Listener->beforeRender(new Event('beforeRender', new View()));
        $this->assertArrayHasKey('BlogContent1', Configure::read('BcApp.adminNavigation.Contents'));
    }

    /**
     * Test setAdminMenu
     *
     * @return void
     */
    public function testSetAdminMenu(): void
    {
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'template' => 'default',
            'list_count' => '10',
            'list_direction' => 'DESC',
            'feed_count' => '10',
            'tag_use' => '1',
            'comment_use' => '1',
            'comment_approve' => '0',
            'widget_area' => '2',
            'eye_catch_size' => '',
            'use_content' => '1'
        ])->persist();
        ContentFactory::make([
            'id' => 1,
            'url' => '/',
            'name' => '',
            'title' => 'test',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'site_id' => 1,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 10,
            'entity_id' => 1,
            'site_root' => true,
            'status' => true
        ])->persist();
        $this->Listener->setAdminMenu();
        $config = Configure::read('BcApp.adminNavigation.Contents');
        $this->assertArrayHasKey('BlogContent1', $config);
        $this->assertEquals(1, $config['BlogContent1']['siteId']);
        $this->assertEquals('test', $config['BlogContent1']['title']);
        $this->assertEquals('blog-content', $config['BlogContent1']['type']);
    }

    /**
     * Test leftOfToolbar
     *
     * @return void
     */

    public function testLeftOfToolbar(): void
    {
        //with isAdminSystem false
        $this->loginAdmin($this->getRequest('/abc'));
        $View = new BcAdminAppView();
        ob_start();
        $this->Listener->leftOfToolbar(new Event('leftOfToolbar', $View));
        $result = ob_get_clean();
        $this->assertEmpty($result);

        //with isAdminSystem true
        $this->loginAdmin($this->getRequest('/'));
        BlogContentFactory::make([
            'id' => '1',
            'template' => 'default',
            'use_content' => '1'
        ])->persist();
        ContentFactory::make([
            'id' => 1,
            'url' => '/',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'site_id' => 1,
            'entity_id' => 1,
        ])->persist();
        $View = new BcAdminAppView();
        ob_start();
        $this->Listener->leftOfToolbar(new Event('leftOfToolbar', $View));
        $result = ob_get_clean();
        $this->assertNotNull($result);
    }

}
