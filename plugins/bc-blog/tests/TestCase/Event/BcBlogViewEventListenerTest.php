<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Test.Case.Model
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\Event;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Event\BcBlogViewEventListener;
use BcBlog\Test\Factory\BlogContentsFactory;
use Cake\Core\Configure;
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
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
    ];

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Listener = new BcBlogViewEventListener();
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
        $this->loadFixtureScenario(InitAppScenario::class);
        BlogContentsFactory::make([
            'id' => '1',
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'template' => 'default',
            'list_count' => '10',
            'list_direction' => 'DESC',
            'feed_count' => '10',
            'tag_use' => '1',
            'comment_use' => '1',
            'comment_approve' => '0',
            'auth_captcha' => '1',
            'widget_area' => '2',
            'eye_catch_size' => '',
            'use_content' => '1'
        ])->persist();
        ContentFactory::make([
            'id' => 1,
            'url' => '/',
            'name' => '',
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
        $this->Listener->beforeRender();
        $config = Configure::read('BcApp.adminNavigation.Contents');
        $this->assertArrayNotHasKey('BlogContent1', $config);
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->Listener->beforeRender();
        $config = Configure::read('BcApp.adminNavigation.Contents');
        $this->assertArrayHasKey('BlogContent1', $config);
    }

    /**
     * Test setAdminMenu
     *
     * @return void
     */
    public function testSetAdminMenu(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
