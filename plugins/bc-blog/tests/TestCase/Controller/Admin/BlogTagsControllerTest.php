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
use BcBlog\Controller\Admin\BlogTagsController;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogTagsControllerTest
 *
 * @property BlogTagsController $BlogTagsController
 */
class BlogTagsControllerTest extends BcTestCase
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
        'plugin.BcBlog.Factory/BlogPosts',
        'plugin.BcBlog.Factory/BlogTags',
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
        $this->BlogTagsController = new BlogTagsController($this->loginAdmin($this->getRequest()));
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
    public function test_initialize()
    {
        $this->assertNotEmpty($this->BlogTagsController->BcAdminContents);
    }

    /**
     * test add
     */
    public function test_add(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //実行成功場合、
        $data = [
            'name' => 'test add'
        ];
        $this->post('/baser/admin/bc-blog/blog_tags/add', $data);
        //リダイレクトを確認
        $this->assertResponseCode(302);
        $this->assertRedirect(['action' => 'index']);
        //メッセージを確認
        $this->assertFlashMessage('タグ「test add」を追加しました。');

        //実行失敗場合、
        $data = [
            'name' => null
        ];
        $this->post('/baser/admin/bc-blog/blog_tags/add', $data);
        //メッセージを確認
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals(['name' => ['_empty' => "ブログタグを入力してください。"]], $vars['blogTag']->getErrors());
        //リダイレクトしないを確認
        $this->assertResponseCode(200);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test index
     */
    public function test_index()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
