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
use BaserCore\Test\Factory\ContentFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
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
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest('/baser/admin/bc-blog/blog_contents/'));
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
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'test'
        ])->persist();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcBlog.BlogContents.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['description'] = 'Nghiem';
            $event->setData('data', $data);
        });
        $data = [
            'id' => 1,
            'description' => '更新した!',
            'content' => [
                "title" => "更新 ブログ",
            ]
        ];
        $this->post("/baser/admin/bc-blog/blog_contents/edit/1", $data);
        $blogContent = BlogContentFactory::get(1);
        $this->assertEquals('Nghiem', $blogContent['description']);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データを作成
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'test'
        ])->persist();
        //正常系実行
        $data = [
            'id' => 1,
            'description' => 'test edit',
            'content' => [
                "title" => "更新 ブログ",
            ]
        ];
        $this->post("/baser/admin/bc-blog/blog_contents/edit/1", $data);
        $blogContent = BlogContentFactory::get(1);
        $this->assertEquals('test edit', $blogContent['description']);
        //異常系実行
        $this->post("/baser/admin/bc-blog/blog_contents/edit/199", $data);
        $this->assertResponseCode(404);
    }

    /**
     * Test afterEdit method
     *
     * @return void
     */
    public function testAfterEditEvent(): void
    {
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcBlog.BlogContents.afterEdit', function (Event $event) {
            $blogContent = $event->getData('data');
            $blogContents = TableRegistry::getTableLocator()->get('BaserCore.BlogContents');
            $blogContent->description = 'Nghiem';
            $blogContents->save($blogContent);
        });
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'test'
        ])->persist();
        $data = [
            'id' => 1,
            'description' => '更新した!',
            'content' => [
                "title" => "更新 ブログ",
            ]
        ];
        $this->post("/baser/admin/bc-blog/blog_contents/edit/1", $data);
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcBlog.BlogContents.afterEdit', function (Event $event) {
            $blogContent = $event->getData('data');
            $blogContents = $this->getTableLocator()->get('BaserCore.BlogContents');
            $blogContent->description = 'Nghiem';
            $blogContents->save($blogContent);
        });
        $blogContent = BlogContentFactory::get(1);
        $this->assertEquals('Nghiem', $blogContent['description']);
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
        $controller = new BlogContentsController($this->getRequest());
        $rs = $this->execPrivateMethod($controller, 'redirectEditBlog', ['BcColumn']);
        $this->assertEquals('https://localhost/baser/admin/bc-theme-file/theme_files/edit/BcFront/BcBlog/etc/Blog/BcColumn/index.php', $rs->getHeader('Location')[0]);
        $this->assertEquals(302, $rs->getStatusCode());
    }

}
