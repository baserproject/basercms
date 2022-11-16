<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Test.Case.Controller
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Admin\BlogCommentsController;
use BcBlog\Test\Factory\BlogCommentFactory;
use BcBlog\Test\Scenario\BlogCommentsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogCommentsControllerTest
 *
 * @property  BlogCommentsController $Controller
 */
class BlogCommentsControllerTest extends BcTestCase
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
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BcBlog.Factory/BlogComments',
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
        $this->Controller = new BlogCommentsController($this->loginAdmin($this->getRequest()));
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test index
     */
    public function testIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test delete
     */
    public function testDelete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(BlogCommentsScenario::class);

        $this->delete("/baser/admin/bc-blog/blog_comments/delete/1/1");
        $this->assertResponseCode(302);
        $this->assertRedirect(['action' => 'index/1']);
        $this->assertFlashMessage('ブログコメント No1 を削除しました。');

        $this->delete("/baser/admin/bc-blog/blog_comments/delete/1/2?blog_post_id=1");
        $this->assertRedirect(['action' => 'index/1?blog_post_id=1']);
    }

    /**
     * test captcha
     */
    public function testCaptcha()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test publish
     */
    public function testPublish()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(BlogCommentsScenario::class);

        $this->post("/baser/admin/bc-blog/blog_comments/publish/1/3");
        $this->assertResponseCode(302);
        $this->assertRedirect(['action' => 'index/1']);
        $this->assertFlashMessage('ブログコメント No.3 を公開状態にしました。');
        $this->assertTrue(BlogCommentFactory::get(3)->status);

        $this->post("/baser/admin/bc-blog/blog_comments/publish/1/3?blog_post_id=1");
        $this->assertRedirect(['action' => 'index/1?blog_post_id=1']);
    }

    /**
     * test unpublish
     */
    public function testUnpublish()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(BlogCommentsScenario::class);

        $this->post("/baser/admin/bc-blog/blog_comments/unpublish/1/2");
        $this->assertResponseCode(302);
        $this->assertRedirect(['action' => 'index/1']);
        $this->assertFlashMessage('ブログコメント No.2 を非公開状態にしました。');
        $this->assertFalse(BlogCommentFactory::get(2)->status);

        $this->post("/baser/admin/bc-blog/blog_comments/unpublish/1/2?blog_post_id=1");
        $this->assertRedirect(['action' => 'index/1?blog_post_id=1']);
    }

    /**
     * test get_token
     */
    public function testGet_token()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
