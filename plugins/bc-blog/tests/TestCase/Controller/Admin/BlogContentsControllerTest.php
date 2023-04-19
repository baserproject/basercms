<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Admin\BlogContentsController;
use BcBlog\Test\Factory\BlogContentFactory;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogContentsControllerTest
 *
 * @property  BlogContentsController $BlogContentsController
 */
class BlogContentsControllerTest extends BcTestCase
{
    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcBlog.Factory/BlogCategories',
        'plugin.BcBlog.Factory/BlogContents',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->Controller = new BlogContentsController($this->loginAdmin($this->getRequest()));
        $request = $this->getRequest('/baser/admin/bc-blog/blog_contents/');
        $request = $this->loginAdmin($request);
        $this->BlogContentsController = new BlogContentsController($request);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown():void
    {
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $controller = new BlogContentsController($this->getRequest());
        $this->assertNotEmpty($controller->BcAdminContents);
    }

    /**
     * Test beforeEdit method
     *
     * @return void
     */
    public function testBeforeEditEvent(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'test'
        ])->persist();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcBlog.BlogContents.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['description'] = 'Nghiem';
            $event->setData('data', $data);
        });
        $data = ['description' => 'editedName'];
        $this->post("/baser/admin/bc-blog/blog_contents/edit/1", $data);
        $blogContent = BlogContentFactory::get(1);
        $this->assertEquals('Nghiem', $blogContent['description']);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test redirectEditLayout
     */
    public function test_redirectEditLayout()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test redirectEditBlog
     */
    public function test_redirectEditBlog()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
