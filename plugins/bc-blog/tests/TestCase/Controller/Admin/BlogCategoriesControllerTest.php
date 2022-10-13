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

use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Admin\BlogCategoriesController;
use BcBlog\Test\Factory\BlogCategoryFactory;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class BlogCategoriesControllerTest
 *
 * @package Blog.Test.Case.Controller
 * @property  BlogCategoriesController $Controller
 */
class BlogCategoriesControllerTest extends BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.Sites',
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
        $this->Controller = new BlogCategoriesController($this->loginAdmin($this->getRequest()));
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
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] ブログを一覧表示する
     */
    public function testAdmin_index()
    {
        $this->get('/baser/admin/bc-blog/blog_categories/index');
        $this->assertResponseError();
        $this->get('/baser/admin/bc-blog/blog_categories/index/1');
        $this->assertResponseOk();
    }

    /**
     * [ADMIN] 登録処理
     */
    public function testAdmin_add()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 編集処理
     */
    public function testAdmin_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 削除処理
     */
    public function testAdmin_delete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        BlogCategoryFactory::make([
            'id' => '1',
            'blog_content_id' => '1',
            'no' => '1',
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => '1',
            'parent_id' => null,
            'lft' => '1',
            'rght' => '4',
            'owner_id' => '1',
            'created' => '2015-01-27 12:56:53',
            'modified' => null
        ])->persist();
        $this->delete("/baser/admin/bc-blog/blog_categories/delete/1/1");
        // ステータスを確認
        $this->assertResponseCode(302);
        // メッセージを確認
        $this->assertFlashMessage('release を削除しました。');
        // リダイレクトを確認
        $this->assertRedirect([
            'plugin' => 'BcBlog',
            'prefix' => 'Admin',
            'controller' => 'blog_categories',
            'action' => 'index/1'
        ]);
        // データの削除を確認
        $this->assertEquals(0, BlogCategoryFactory::count());
    }

}
